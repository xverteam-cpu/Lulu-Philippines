<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalConfirmation;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()?->is_admin, 403);

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'pending');

        $withdrawals = Withdrawal::query()
            ->with(['user', 'approver'])
            ->where('status', $status)
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('account_holder', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $approvedCount = Withdrawal::where('status', 'approved')->count();
        $rejectedCount = Withdrawal::where('status', 'rejected')->count();

        return view('admin.withdrawals', [
            'withdrawals' => $withdrawals,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(Withdrawal $withdrawal): View
    {
        return view('admin.withdrawal-show', [
            'withdrawal' => $withdrawal->load(['user', 'approver']),
        ]);
    }

    public function approve(Withdrawal $withdrawal, Request $request): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()
                ->withErrors(['error' => 'Only pending withdrawals can be approved.']);
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        try {
            $withdrawal->load('user');
            Mail::to($withdrawal->user->email)
                ->send(new WithdrawalConfirmation($withdrawal));
        } catch (\Throwable $e) {
            Log::warning('Failed to send approved withdrawal email', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->back()
            ->with('status', 'Withdrawal approved successfully.');
    }

    public function reject(Withdrawal $withdrawal, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()
                ->withErrors(['error' => 'Only pending withdrawals can be rejected.']);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        if ($withdrawal->user) {
            $withdrawal->user->balance = ($withdrawal->user->balance ?? 0) + (float) $withdrawal->amount;
            $withdrawal->user->save();
        }

        return redirect()
            ->back()
            ->with('status', 'Withdrawal rejected successfully.');
    }
}
