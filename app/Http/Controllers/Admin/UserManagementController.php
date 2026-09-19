<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PackageGiftedEmail;
use App\Mail\LuluPromotionEmail;
use App\Models\Investment;
use App\Models\PackageSlot;
use App\Models\ReferralEarning;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\InvestmentPackages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::user()?->is_admin, 403);

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.dashboard', [
            'users' => $users,
            'totalUsers' => User::count(),
            'newUsersCount' => User::where('created_at', '>=', now()->subDay())->count(),
            'adminUsersCount' => User::where('is_admin', true)->count(),
            'onlineUsersCount' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            'approvedDepositTotal' => Investment::where('status', 'approved')->sum('amount'),
            'pendingWithdrawalsCount' => Withdrawal::where('status', 'pending')->count(),
            'search' => $search,
            'packages' => InvestmentPackages::all(),
            'packageSlots' => InvestmentPackages::currentSlots(),
        ]);
    }

    public function show(User $user): View
    {
        return view('admin.user-show', [
            'managedUser' => $user,
        ]);
    }

    public function backup(): \Illuminate\Http\JsonResponse
    {
        $backupPayload = [
            'exported_at' => now()->toIso8601String(),
            'site' => 'Lulu',
            'users' => User::query()
                ->orderBy('id')
                ->get()
                ->map(function (User $user) {
                    return $user->makeVisible(['password', 'remember_token'])->toArray();
                })
                ->values(),
            'investments' => Investment::query()->orderBy('id')->get()->map->toArray()->values(),
            'withdrawals' => Withdrawal::query()->orderBy('id')->get()->map->toArray()->values(),
            'referral_earnings' => ReferralEarning::query()->orderBy('id')->get()->map->toArray()->values(),
            'package_slots' => PackageSlot::query()->orderBy('id')->get()->map->toArray()->values(),
            'investment_packages' => InvestmentPackages::all(),
        ];

        $filename = 'lotteria-backup-'.now()->format('Y-m-d-H-i-s').'.json';

        return response()->json($backupPayload, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['user' => 'Admin accounts cannot be deleted.']);
        }

        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', 'User account deleted successfully.');
    }

    public function restrict(User $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['user' => 'Admin accounts cannot be restricted.']);
        }

        $user->forceFill([
            'is_restricted' => true,
            'restricted_ip_address' => $user->last_ip_address ?: null,
        ])->save();

        return redirect()->route('admin.dashboard')
            ->with('status', 'User access restricted successfully.');
    }

    public function sendPackage(Request $request)
    {
        $packages = InvestmentPackages::all();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package' => 'required|in:'.implode(',', array_keys($packages)),
        ]);

        $user = User::findOrFail($request->user_id);
        $package = $packages[$request->package];

        $created = DB::transaction(function () use ($user, $package, $request) {
            if (! InvestmentPackages::reserveSlot($request->package)) {
                return false;
            }

            $investment = Investment::create([
                'user_id' => $user->id,
                'package_key' => $request->package,
                'package_name' => $package['name'],
                'package_price' => $package['price'],
                'amount' => $package['price'],
                'daily_interest_rate' => $package['daily_interest_rate'],
                'duration_days' => $package['duration_days'],
                'payment_method' => 'admin_transfer',
                'status' => 'approved',
                'starts_at' => now(),
            ]);

            $investment->refresh();
            $investment->accrueDailyInterest();

            try {
                Mail::to($investment->user->email)->send(new PackageGiftedEmail($investment));
            } catch (\Throwable $e) {
                Log::warning('Failed to send package gift email', [
                    'investment_id' => $investment->id,
                    'user_id' => $investment->user_id,
                    'error' => $e->getMessage(),
                ]);
            }

            $investment->processReferralCommission();

            return true;
        });

        if (! $created) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['package' => "No remaining slots available for {$package['name']}."]);
        }

        return redirect()->route('admin.dashboard')
            ->with('status', "Package '{$package['name']}' sent to {$user->name} successfully!");
    }

    public function updatePackageSlots(Request $request)
    {
        $defaultPackageKeys = array_keys(InvestmentPackages::defaults());
        $rules = ['slots' => 'required|array'];

        foreach ($defaultPackageKeys as $key) {
            $rules["slots.{$key}"] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        foreach ($validated['slots'] as $packageKey => $remainingSlots) {
            InvestmentPackages::setRemainingSlots($packageKey, $remainingSlots);
        }

        return redirect()->route('admin.dashboard')
            ->with('status', 'Package slot counts updated successfully.');
    }

    public function sendFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = (float) $request->amount;

        // Add funds to user balance
        $user->balance += $amount;
        $user->save();

        return redirect()->route('admin.dashboard')
            ->with('status', "Sent \${$amount} to {$user->name} successfully! New balance: \${$user->balance}");
    }
    public function sendPromotionalEmail()
    {
        $sentCount = 0;

        User::query()
            ->whereNotNull('email')
            ->chunkById(100, function ($users) use (&$sentCount) {
                foreach ($users as $user) {
                    try {
                        Mail::to($user->email)->send(new LuluPromotionEmail($user));
                        $sentCount++;
                    } catch (\Throwable $e) {
                        Log::warning('Failed to send promotional email', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        return redirect()->route('admin.dashboard')
            ->with('status', "Promotional email has been sent to {$sentCount} users.");
    }}
