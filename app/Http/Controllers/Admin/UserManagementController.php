<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LuluPromotionEmail;
use App\Mail\PackageGiftedEmail;
use App\Models\BlockedIp;
use App\Models\AccountActivity;
use App\Models\Investment;
use App\Models\PackageSlot;
use App\Models\ReferralEarning;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\InvestmentPackages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $temporaryPassword = $request->session()->pull('temporary_password');
        $temporaryPasswordUser = $request->session()->pull('temporary_password_user');

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
        $blockedIps = BlockedIp::query()
            ->whereIn('ip_address', $users->getCollection()->pluck('last_ip_address')->filter()->unique())
            ->get()
            ->keyBy('ip_address');

        return view('admin.dashboard', [
            'users' => $users,
            'temporaryPassword' => $temporaryPassword,
            'temporaryPasswordUser' => $temporaryPasswordUser,
            'referrers' => User::query()->orderBy('name')->get(['id', 'name', 'email', 'referral_code']),
            'blockedIps' => $blockedIps,
            'totalUsers' => User::count(),
            'newUsersCount' => User::where('created_at', '>=', now()->subDay())->count(),
            'adminUsersCount' => User::where('is_admin', true)->count(),
            'onlineUsersCount' => User::where('last_seen_at', '>=', now()->subMinutes(User::ONLINE_WINDOW_MINUTES))->count(),
            'approvedDepositTotal' => Investment::where('status', 'approved')->sum('amount'),
            'pendingWithdrawalsCount' => Withdrawal::where('status', 'pending')->count(),
            'recentPurchaseNotifications' => $this->recentPurchaseNotifications(),
            'recentFundsNotifications' => $this->recentFundsNotifications(),
            'search' => $search,
            'packages' => InvestmentPackages::all(),
            'packageSlots' => InvestmentPackages::currentSlots(),
        ]);
    }

    public function purchaseNotifications(): JsonResponse
    {
        return response()->json([
            'notifications' => $this->recentPurchaseNotifications(),
        ]);
    }

    public function fundsNotifications(): JsonResponse
    {
        return response()->json([
            'notifications' => $this->recentFundsNotifications(),
        ]);
    }

    private function recentFundsNotifications(): \Illuminate\Support\Collection
    {
        $cutoff = now()->subDay();

        return AccountActivity::query()
            ->with('user')
            ->where('type', 'funds_received')
            ->where('occurred_at', '>=', $cutoff)
            ->where(function ($query) {
                $query->where('description', 'Funds were added to your account by the LuLu team.')
                    ->orWhere('description', 'like', 'You received funds from %')
                    ->orWhere('description', 'like', 'You received $% from %');
            })
            ->orderByDesc('occurred_at')
            ->get()
            ->map(function (AccountActivity $activity) {
                $isAdminCredit = $activity->description === 'Funds were added to your account by the LuLu team.';

                return [
                    'id' => $activity->id,
                    'type' => $isAdminCredit ? 'admin_funds' : 'user_transfer',
                    'title' => $isAdminCredit ? 'Funds sent by admin' : 'User-to-user funds transfer',
                    'user_name' => $activity->user?->name ?: $activity->user?->email ?: 'Unknown user',
                    'amount' => (float) $activity->amount,
                    'detail' => $activity->description,
                    'occurred_at' => $activity->occurred_at?->toIso8601String(),
                    'timestamp' => $activity->occurred_at?->format('M j, g:i A') ?: '',
                ];
            })
            ->values();
    }

    private function recentPurchaseNotifications(): \Illuminate\Support\Collection
    {
        $cutoff = now()->subDay();

        return Investment::query()
            ->with(['user', 'approver'])
            ->whereNull('cancelled_at')
            ->where(function ($query) use ($cutoff) {
                $query->where(function ($query) use ($cutoff) {
                    $query->where('status', 'approved')
                        ->whereNotNull('approved_by')
                        ->where('approved_at', '>=', $cutoff);
                })->orWhere(function ($query) use ($cutoff) {
                    $query->where('status', 'approved')
                        ->where('payment_method', 'account_balance')
                        ->where('created_at', '>=', $cutoff);
                })->orWhere(function ($query) use ($cutoff) {
                    $query->where('status', 'approved')
                        ->where('payment_method', 'admin_transfer')
                        ->where('created_at', '>=', $cutoff);
                })->orWhere(function ($query) use ($cutoff) {
                    $query->where('status', 'pending')
                        ->where('created_at', '>=', $cutoff);
                });
            })
            ->get()
            ->map(function (Investment $investment) {
                $type = match (true) {
                    $investment->payment_method === 'admin_transfer' => 'admin_package',
                    $investment->status === 'approved' && $investment->approved_by !== null => 'approved',
                    $investment->payment_method === 'account_balance' => 'balance_purchase',
                    default => 'deposit',
                };
                $occurredAt = $type === 'approved'
                    ? $investment->approved_at
                    : $investment->created_at;

                return [
                    'id' => $investment->id.'-'.$type,
                    'type' => $type,
                    'title' => match ($type) {
                        'approved' => 'Bond purchase approved',
                        'admin_package' => 'Package sent by admin',
                        'balance_purchase' => 'Bond purchased using account balance',
                        default => 'New deposit submitted',
                    },
                    'user_name' => $investment->user?->name ?: $investment->user?->email ?: 'Unknown user',
                    'package_name' => $investment->package_name ?: 'Bond',
                    'amount' => (float) $investment->amount,
                    'detail' => match ($type) {
                        'approved' => 'Approved by '.($investment->approver?->name ?: 'admin'),
                        'admin_package' => 'Sent by admin',
                        'balance_purchase' => 'Paid using account balance',
                        default => 'Payment submission awaiting admin review',
                    },
                    'occurred_at' => $occurredAt?->toIso8601String(),
                    'timestamp' => $occurredAt?->format('M j, g:i A') ?: '',
                ];
            })
            ->sortByDesc('occurred_at')
            ->values();
    }

    public function activity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['sometimes', 'array', 'max:50'],
            'user_ids.*' => ['integer', 'distinct'],
        ]);

        $usersQuery = User::query();
        if (isset($validated['user_ids'])) {
            $usersQuery->whereIn('id', $validated['user_ids']);
        } else {
            $usersQuery->where('last_seen_at', '>=', now()->subMinutes(User::ONLINE_WINDOW_MINUTES));
        }

        $users = $usersQuery
            ->whereNotNull('last_seen_at')
            ->get(['id', 'last_seen_at']);
        $onlineUserIds = $users
            ->filter(fn (User $user) => $user->isOnline())
            ->pluck('id')
            ->values();

        return response()->json([
            'online_user_ids' => $onlineUserIds,
            'online_users_count' => User::query()
                ->where('last_seen_at', '>=', now()->subMinutes(User::ONLINE_WINDOW_MINUTES))
                ->count(),
            'last_seen_at' => $users->mapWithKeys(fn (User $user) => [
                (string) $user->id => $user->last_seen_at?->toIso8601String(),
            ]),
        ]);
    }

    public function searchSuggestions(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::query()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json(['users' => $users]);
    }

    public function show(User $user): View
    {
        return view('admin.user-show', [
            'managedUser' => $user,
        ]);
    }

    public function backup(): JsonResponse
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

    public function destroyInvestment(User $user, Investment $investment): RedirectResponse
    {
        $result = DB::transaction(function () use ($user, $investment): string {
            $investment = $user->investments()
                ->whereKey($investment->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($investment->cancelled_at) {
                return 'already_deleted';
            }

            if ($investment->status !== 'approved') {
                return 'not_active';
            }

            $investment->accrueDailyInterest();
            $investment->forceFill(['cancelled_at' => now()])->save();

            return 'deleted';
        });

        if ($result === 'already_deleted') {
            return redirect()->route('admin.dashboard')
                ->withErrors(['investment' => 'This package has already been deleted.']);
        }

        if ($result === 'not_active') {
            return redirect()->route('admin.dashboard')
                ->withErrors(['investment' => 'Only active packages can be deleted.']);
        }

        return redirect()->route('admin.dashboard')
            ->with('status', 'Package deleted. Previously credited income was kept and no further interest will accrue.');
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

    public function blockIp(User $user, Request $request): RedirectResponse
    {
        if ($user->is_admin) {
            return back()->withErrors(['user' => 'Admin IP addresses cannot be blocked from the user list.']);
        }

        $ipAddress = $user->last_ip_address;

        if (! $ipAddress || filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            return back()->withErrors(['ip_address' => 'This user does not have a valid recorded IP address to block.']);
        }

        if ($ipAddress === $request->ip()) {
            return back()->withErrors(['ip_address' => 'You cannot block the IP address you are currently using.']);
        }

        BlockedIp::firstOrCreate(
            ['ip_address' => $ipAddress],
            [
                'user_id' => $user->id,
                'blocked_by' => $request->user()->id,
            ]
        );

        return redirect()->route('admin.dashboard')
            ->with('status', "IP address {$ipAddress} blocked successfully.");
    }

    public function createTemporaryPassword(User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['user' => 'Temporary passwords cannot be created for admin accounts.']);
        }

        $temporaryPassword = Str::random(20);

        $user->forceFill([
            'temporary_password_hash' => Hash::make($temporaryPassword),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('admin.dashboard')
            ->with('temporary_password', $temporaryPassword)
            ->with('temporary_password_user', $user->name ?: $user->email);
    }

    public function unblockIp(BlockedIp $blockedIp): RedirectResponse
    {
        $ipAddress = $blockedIp->ip_address;
        $blockedIp->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', "IP address {$ipAddress} unblocked successfully.");
    }

    public function sendPackage(Request $request)
    {
        $packages = InvestmentPackages::all();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'package' => 'required|in:'.implode(',', array_keys($packages)),
            'amount' => 'required|numeric|decimal:0,2',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $package = $packages[$validated['package']];
        $amount = $validated['amount'];

        if ((float) $amount < $package['min_amount'] || (float) $amount > $package['max_amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Amount for '.$package['name'].' must be between $'.number_format($package['min_amount'], 2).' and $'.number_format($package['max_amount'], 2).'.',
            ]);
        }

        $investment = DB::transaction(function () use ($user, $package, $validated, $amount): Investment|false {
            if (! InvestmentPackages::reserveSlot($validated['package'])) {
                return false;
            }

            $investment = Investment::create([
                'user_id' => $user->id,
                'package_key' => $validated['package'],
                'package_name' => $package['name'],
                'package_price' => $amount,
                'amount' => $amount,
                'daily_interest_rate' => $package['daily_interest_rate'],
                'duration_days' => $package['duration_days'],
                'payment_method' => 'admin_transfer',
                'status' => 'approved',
                'starts_at' => now(),
            ]);

            $investment->processReferralCommission();

            return $investment;
        });

        if (! $investment) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['package' => "No remaining slots available for {$package['name']}."]);
        }

        $recipientEmail = trim((string) $user->email);
        if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL) !== false) {
            try {
                Mail::to($recipientEmail)->send(new PackageGiftedEmail($investment));
            } catch (\Throwable $e) {
                Log::warning('Failed to send package gift email', [
                    'investment_id' => $investment->id,
                    'user_id' => $investment->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('Skipped package gift email because the recipient email address is invalid', [
                'investment_id' => $investment->id,
                'user_id' => $investment->user_id,
            ]);
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

        $amount = (float) $request->amount;
        $user = DB::transaction(function () use ($request, $amount): User {
            $user = User::query()->lockForUpdate()->findOrFail($request->user_id);
            $user->balance += $amount;
            $user->save();

            AccountActivity::create([
                'user_id' => $user->id,
                'type' => 'funds_received',
                'title' => 'Funds received',
                'description' => 'Funds were added to your account by the LuLu team.',
                'amount' => $amount,
                'occurred_at' => now(),
            ]);

            return $user;
        });

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
    }
}
