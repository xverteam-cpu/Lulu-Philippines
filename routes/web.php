<?php

use App\Http\Controllers\Admin\InvestmentApprovalController;
use App\Http\Controllers\Admin\AccountInvitationController;
use App\Http\Controllers\Admin\ClientContractController;
use App\Http\Controllers\Admin\IdentityVerificationController as AdminIdentityVerificationController;
use App\Http\Controllers\Admin\FranchiseApplicationController as AdminFranchiseApplicationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountSetupController;
use App\Http\Controllers\FranchiseApplicationController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\InvestorMarketQuoteController;
use App\Http\Controllers\InvestorPinController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Middleware\RequireInvestorPin;
use App\Http\Middleware\RestrictUserAccess;
use App\Mail\WithdrawalConfirmation;
use App\Models\AccountActivity;
use App\Models\Investment;
use App\Models\PushSubscription;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\CurrencyRateService;
use App\Support\CurrencyDisplay;
use App\Support\DailyInterestAccrualService;
use App\Support\InvestmentPackages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return redirect()->route('investors');
})->name('public.home');

Route::get('/about', function () {
    return response(file_get_contents(base_path('LuLu-Philippines-Client-Hub(1).html')), 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->name('about');

Route::get('/privacy-policy', function () {
    return response(file_get_contents(public_path('privacy-policy.html')), 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return response(file_get_contents(public_path('terms-of-service.html')), 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->name('terms-of-service');

Route::get('/payment-options', function () {
    $providers = [
        'bpi' => [
            'name' => 'BPI',
            'qr' => 'BPIQR.png',
            'initials' => 'BPI',
            'background' => '#005baa',
            'accent' => '#d71920',
        ],
        'landbank' => [
            'name' => 'Landbank',
            'qr' => 'LandbankQR.png',
            'initials' => 'LANDBANK',
            'background' => '#006b3f',
            'accent' => '#f5c542',
        ],
        'palawanpay' => [
            'name' => 'Palawan Pay',
            'qr' => 'PalawanPay.png',
            'initials' => 'PALAWAN',
            'background' => '#078b4f',
            'accent' => '#f4c542',
        ],
    ];
    $providerKey = (string) request()->query('provider', 'bpi');
    abort_unless(isset($providers[$providerKey]), 404);

    return view('public.payment-options', [
        'providers' => $providers,
        'providerKey' => $providerKey,
        'provider' => $providers[$providerKey],
    ]);
})->name('payment-options');

Route::get('/investors', function () {
    return view('public.investors');
})->name('investors');

Route::get('/investors/market-quote', InvestorMarketQuoteController::class)
    ->middleware('throttle:30,1')
    ->name('investors.market-quote');

Route::get('/home', function () {
    return redirect()->route('investors');
})->name('home');

Route::get('/order', function () {
    return redirect()->route('investors');
})->name('order');

Route::get('/login', function () {
    return redirect()->route('investors');
})->name('login');

Route::get('/login/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('login.google.callback');

Route::get('/debug/test-email', function (Request $request) {
    if (app()->environment('production')) {
        abort(403, 'Test email route is disabled in production.');
    }

    $email = $request->query('email', 'test@example.com');

    $user = new User([
        'name' => 'Test User',
        'email' => $email,
    ]);

    $withdrawal = new Withdrawal([
        'user_id' => 999999,
        'amount' => 250.00,
        'processing_fee' => 12.50,
        'total_withdrawn' => 237.50,
        'transaction_reference' => 'WD-TEST-'.strtoupper((string) Str::random(8)),
        'payment_method' => 'bank_transfer',
        'bank_name' => 'Test Bank',
        'account_number' => '1234567890',
        'account_holder' => 'Test User',
        'status' => 'pending',
    ]);
    $withdrawal->setRelation('user', $user);

    Mail::to($email)->send(new WithdrawalConfirmation($withdrawal));

    return redirect()->route('withdraw')->with('status', 'Test withdrawal email sent.');
})->name('debug.test-email');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/temporary-password/change', [AuthController::class, 'showTemporaryPasswordChange'])
    ->name('temporary-password.change');
Route::post('/temporary-password/change', [AuthController::class, 'changeTemporaryPassword'])
    ->middleware('throttle:5,1')
    ->name('temporary-password.update');
Route::post('/register-partner', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.partner');
Route::get('/account/setup/{token}', [AccountSetupController::class, 'show'])
    ->middleware('throttle:10,1')
    ->name('account-setup.show');
Route::post('/account/setup/{token}', [AccountSetupController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('account-setup.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/account/phone', [PhoneNumberController::class, 'show'])
    ->middleware('auth')
    ->name('phone.capture');
Route::post('/account/phone', [PhoneNumberController::class, 'store'])
    ->middleware(['auth', 'throttle:5,1'])
    ->name('phone.store');
Route::post('/investor/pin/verify', [InvestorPinController::class, 'verify'])
    ->middleware(['auth', 'throttle:5,1'])
    ->name('investor.pin.verify');
Route::post('/investor/pin/setup', [InvestorPinController::class, 'setup'])
    ->middleware(['auth', 'throttle:5,1'])
    ->name('investor.pin.setup');
Route::post('/activity/heartbeat', function () {
    return response()->noContent();
})->middleware(['auth', RestrictUserAccess::class])->name('activity.heartbeat');

Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
Route::get('/forgot-pin', [PasswordResetController::class, 'pinRequestForm'])->name('pin.request');
Route::post('/forgot-pin', [PasswordResetController::class, 'sendPinResetLink'])->middleware('throttle:5,1')->name('pin.email');
Route::get('/reset-pin/{token}', [PasswordResetController::class, 'pinResetForm'])->name('pin.reset');
Route::post('/reset-pin', [PasswordResetController::class, 'resetPin'])->middleware('throttle:5,1')->name('pin.update');

Route::get('/signup', function () {
    return view('signup', ['referral' => request('ref')]);
})->name('signup');

Route::get('/franchising', function () {
    return view('franchising', ['referral' => request('ref')]);
})->name('franchising');

Route::get('/insurance', function () {
    $insurancePath = base_path('Car-Insurance.html');
    abort_unless(is_file($insurancePath), 500, 'The car insurance page is unavailable.');

    $insuranceHtml = file_get_contents($insurancePath);
    abort_if($insuranceHtml === false, 500, 'The car insurance page could not be read.');

    return response($insuranceHtml, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('insurance');

Route::post('/franchise-applications', [FranchiseApplicationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('franchise-applications.store');

Route::get('/unavailable', function () {
    return view('unavailable');
})->name('unavailable');

Route::get('/dashboard', function () {
    if (Auth::user()?->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    $user = Auth::user();
    $displayCurrency = CurrencyDisplay::preferredFor($user);
    $displayRate = $displayCurrency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : 1.0;
    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user = $user->fresh();  // Refresh from database to get updated balance
    $identityVerification = $user->identityVerification;

    $recentInvestments = Investment::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    $recentWithdrawals = Withdrawal::where('user_id', $user->id)
        ->notSignupBonus()
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    $approvedInvestments = $user->investments()
        ->where('status', 'approved')
        ->whereNull('cancelled_at')
        ->get();
    $pendingContracts = $user->investments()
        ->whereIn('status', ['pending', 'approved'])
        ->whereIn('payment_method', ['bank_transfer', 'e_wallet', 'account_balance', 'admin_transfer'])
        ->whereNull('cancelled_at')
        ->whereNull('agreement_signed_at')
        ->latest()
        ->get();
    $dailyInterest = $approvedInvestments->sum(fn ($investment) => $investment->dailyInterestAmount());

    if (! is_numeric($dailyInterest)) {
        $dailyInterest = 0;
    }

    $notifications = collect();

    foreach ($approvedInvestments as $investment) {
        $creditedDays = min((int) $investment->interest_days_credited, (int) $investment->duration_days);
        $firstInterestDate = $investment->effectiveStartDate()->copy()->startOfDay()->addDay();

        for ($day = max(0, $creditedDays - 50); $day < $creditedDays; $day++) {
            $interestDate = $firstInterestDate->copy()->addDays($day);
            $notifications->push([
                'id' => 'interest-'.$investment->id.'-'.$interestDate->toDateString(),
                'title' => 'Daily interest credited',
                'description' => CurrencyDisplay::formatUsd($investment->dailyInterestAmount(), $displayCurrency, $displayRate).' added to available balance.',
                'time' => $interestDate,
            ]);
        }
    }

    foreach ($recentInvestments as $investment) {
        $description = match ($investment->payment_method) {
            'admin_transfer' => 'Package sent by admin: '.$investment->package_name.'.',
            'account_balance' => 'Investment purchased from your balance: '.$investment->package_name.'.',
            'bank_transfer' => 'Investment submitted and pending approval: '.$investment->package_name.'.',
            default => 'Investment activity: '.$investment->package_name.'.',
        };

        $notifications->push([
            'id' => 'investment-'.$investment->id.'-'.$investment->status,
            'title' => 'Investment update',
            'description' => $description,
            'time' => $investment->created_at,
        ]);
    }

    foreach ($recentWithdrawals as $withdrawal) {
        $notifications->push([
            'id' => 'withdrawal-'.$withdrawal->id.'-'.$withdrawal->status,
            'title' => 'Withdrawal request',
            'description' => CurrencyDisplay::formatUsd($withdrawal->amount, $displayCurrency, $displayRate).' '.$withdrawal->status.'.',
            'time' => $withdrawal->created_at,
        ]);
    }

    $notifications = $notifications
        ->sortByDesc('time')
        ->values()
        ->take(5);

    $notificationsRead = $user->getNotificationsReadIds();
    $unreadCount = $notifications->reject(fn ($notification) => in_array($notification['id'], $notificationsRead, true))->count();

    return view('dashboard_user', [
        'user' => $user,
        'identityVerification' => $identityVerification,
        'notifications' => $notifications,
        'notificationsRead' => $notificationsRead,
        'unreadCount' => $unreadCount,
        'pendingContracts' => $pendingContracts,
    ]);
})->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])->name('dashboard');

Route::post('/currency-preference', function (Request $request) {
    $data = $request->validate([
        'preferred_currency' => ['required', 'in:USD,PHP'],
    ]);

    abort_if($request->user()->is_admin, 403);
    $request->user()->update(['preferred_currency' => $data['preferred_currency']]);

    return redirect()->route('dashboard')->with('status', 'Currency preference updated.');
})->middleware(['auth', RestrictUserAccess::class])->name('currency.preference');

Route::post('/notifications/read-all', function (Request $request) {
    $data = $request->validate([
        'ids' => ['required', 'array'],
        'ids.*' => ['string'],
    ]);

    if ($request->expectsJson()) {
        $request->user()->markNotificationsRead($data['ids']);

        return response()->json(['status' => 'success']);
    }

    $request->user()->markNotificationsRead($data['ids']);

    return redirect()->route('notifications.index')->with('status', 'Notifications marked as read.');
})->middleware(['auth'])->name('notifications.read_all');

Route::get('/notifications', function (Request $request) {
    $user = $request->user();
    $displayCurrency = CurrencyDisplay::preferredFor($user);
    $rate = $displayCurrency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : 1.0;
    $activities = collect();

    foreach (AccountActivity::query()->where('user_id', $user->id)->latest('occurred_at')->limit(50)->get() as $activity) {
        $description = str_replace(
            '$'.number_format((float) $activity->amount, 2),
            CurrencyDisplay::formatUsd((float) $activity->amount, $displayCurrency, $rate),
            $activity->description
        );
        $activities->push([
            'id' => 'activity-'.$activity->id,
            'title' => $activity->title,
            'description' => $description,
            'amount' => $activity->amount,
            'amount_prefix' => '$',
            'href' => route('history'),
            'occurred_at' => $activity->occurred_at,
        ]);
    }

    $investments = $user->investments()->latest()->limit(50)->get();

    foreach ($investments as $investment) {
        $isDeposit = $investment->payment_method === 'bank_transfer';
        $title = match (true) {
            $investment->payment_method === 'admin_transfer' => 'Package received',
            $isDeposit && $investment->status === 'approved' => 'Deposit approved',
            $isDeposit && $investment->status === 'rejected' => 'Deposit declined',
            $isDeposit => 'Deposit request submitted',
            $investment->status === 'rejected' => 'Investment declined',
            default => 'Investment update',
        };
        $description = match (true) {
            $investment->payment_method === 'admin_transfer' => 'The '.$investment->package_name.' package was added to your account.',
            $isDeposit && $investment->status === 'pending' => 'Your deposit for '.$investment->package_name.' is awaiting review.',
            $investment->status === 'approved' => $investment->package_name.' package is active on your account.',
            $investment->status === 'rejected' => $investment->rejection_reason ?: 'Your '.$investment->package_name.' request was not approved.',
            default => $investment->package_name.' package request status: '.$investment->status.'.',
        };

        $activities->push([
            'id' => 'investment-'.$investment->id.'-'.$investment->status,
            'title' => $title,
            'description' => $description,
            'amount' => $isDeposit ? $investment->amount : null,
            'amount_prefix' => '$',
            'href' => route('history'),
            'occurred_at' => $investment->approved_at ?? $investment->updated_at ?? $investment->created_at,
        ]);

        if ($investment->status === 'approved') {
            $creditedDays = min((int) $investment->interest_days_credited, (int) $investment->duration_days);
            $firstInterestDate = $investment->effectiveStartDate()->copy()->startOfDay()->addDay();

            for ($day = max(0, $creditedDays - 50); $day < $creditedDays; $day++) {
                $interestDate = $firstInterestDate->copy()->addDays($day);
                $activities->push([
                    'id' => 'interest-'.$investment->id.'-'.$interestDate->toDateString(),
                    'title' => 'Daily interest credited',
                    'description' => 'Interest credited for '.$investment->package_name.'.',
                    'amount' => $investment->dailyInterestAmount(),
                    'amount_prefix' => '$',
                    'href' => route('history'),
                    'occurred_at' => $interestDate,
                ]);
            }
        }
    }

    foreach ($user->withdrawals()->latest()->limit(20)->get() as $withdrawal) {
        $activities->push([
            'id' => 'withdrawal-'.$withdrawal->id.'-'.$withdrawal->status,
            'title' => match ($withdrawal->status) {
                'approved' => 'Withdrawal approved',
                'rejected' => 'Withdrawal declined',
                default => 'Withdrawal request submitted',
            },
            'description' => $withdrawal->status === 'rejected'
                ? ($withdrawal->rejection_reason ?: 'Your withdrawal was declined and the funds were returned to your balance.')
                : 'Withdrawal request status: '.$withdrawal->status.'.',
            'amount' => $withdrawal->amount,
            'amount_prefix' => '₱',
            'href' => route('history'),
            'occurred_at' => $withdrawal->approved_at ?? $withdrawal->updated_at ?? $withdrawal->created_at,
        ]);
    }

    $verification = $user->identityVerification;
    if ($verification) {
        $activities->push([
            'id' => 'verification-'.$verification->id.'-'.$verification->status.'-'.$verification->updated_at?->timestamp,
            'title' => match ($verification->status) {
                'verified' => 'Identity verification approved',
                'rejected' => 'Identity verification needs attention',
                default => 'Identity verification submitted',
            },
            'description' => match ($verification->status) {
                'verified' => 'Your identity verification was approved.',
                'rejected' => $verification->review_note ?: 'Please review your submission and try again.',
                default => 'Your identity documents are under review.',
            },
            'amount' => null,
            'amount_prefix' => null,
            'href' => route('profile.verification'),
            'occurred_at' => $verification->reviewed_at ?? $verification->submitted_at,
        ]);
    }

    $activities = $activities
        ->sortByDesc('occurred_at')
        ->take(50)
        ->values();
    $readIds = $user->getNotificationsReadIds();
    $unreadCount = $activities->reject(fn (array $activity) => in_array($activity['id'], $readIds, true))->count();

    return view('activity-notifications', [
        'activities' => $activities,
        'readIds' => $readIds,
        'unreadCount' => $unreadCount,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('notifications.index');

// Deposit page for buying shares
Route::get('/deposit', function () {
    return view('deposit');
})->middleware(['auth', RestrictUserAccess::class])->name('deposit');

Route::get('/invest/advertisement', function () {
    return redirect()->route('invest');
})->middleware(['auth', RestrictUserAccess::class])->name('invest.advertisement');

Route::get('/invest', function () {
    $meta = CurrencyRateService::latestUsdToPhpWithMeta();
    $user = Auth::user();
    $totalInvestment = $user
        ? (float) $user->investments()->where('status', 'approved')->whereNull('cancelled_at')->sum('amount')
        : 0;
    $approvedInvestments = collect();
    $packageEarnings = collect();
    $availableBalance = 0;
    if ($user) {
        DailyInterestAccrualService::accrueDueInterestForUser($user);
        $user->refresh();
        $investments = $user->investments()->latest()->get();
        $approvedInvestments = $investments
            ->where('status', 'approved')
            ->whereNull('cancelled_at');
        $packageEarnings = $approvedInvestments
            ->groupBy('package_key')
            ->map(fn ($packageInvestments) => $packageInvestments->sum(fn ($investment) => $investment->creditedInterest()));
        $availableBalance = (float) $user->balance;
    }

    return view('invest', [
        'phpRate' => $meta['rate'],
        'phpRateUpdatedAt' => $meta['updated_at'],
        'packageSlots' => InvestmentPackages::currentSlots(),
        'totalInvestment' => $totalInvestment,
        'availableBalance' => $availableBalance,
        'approvedInvestments' => $approvedInvestments,
        'packageEarnings' => $packageEarnings,
    ]);
})->name('invest');

Route::get('/invest/purchase/{package}', function (string $package) {
    $selectedPackage = InvestmentPackages::find($package);
    abort_unless($selectedPackage, 404);

    $meta = CurrencyRateService::latestUsdToPhpWithMeta();

    return view('invest-purchase', [
        'packageKey' => $package,
        'package' => $selectedPackage,
        'phpRate' => $meta['rate'],
        'phpRateUpdatedAt' => $meta['updated_at'],
        'requiresAgreement' => false,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.purchase');

Route::get('/invest/agreement/sample', function () {
    $path = base_path('lulu-bond-contract-preserved-pages.html');
    abort_unless(is_file($path), 500, 'The sample bond agreement is unavailable.');

    $contractHtml = file_get_contents($path);
    abort_if($contractHtml === false, 500, 'The sample bond agreement could not be read.');

    return response($contractHtml, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.agreement.sample');

Route::get('/invest/agreement/sample/download', function () {
    $path = base_path('LULU_BOND_AGREEMENT_AND_CERTIFICATE_DRAFT_TEMPLATE.docx');
    abort_unless(is_file($path), 404, 'The uploaded bond agreement template is unavailable.');

    return response()->download(
        $path,
        'Lulu-Bond-Agreement-and-Certificate-Draft.docx',
        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
    );
})->middleware(['auth', RestrictUserAccess::class])->name('invest.agreement.sample.download');

Route::get('/invest/agreement/preview', function (Request $request) {
    $package = InvestmentPackages::find((string) $request->query('package'));
    abort_unless($package, 404);

    $currency = $request->query('currency', 'USD');
    abort_unless(in_array($currency, ['USD', 'PHP'], true), 422);
    $amount = (float) $request->query('amount', $package['price']);
    $phpRate = $currency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : null;
    $amountInUsd = $currency === 'PHP'
        ? $amount / $phpRate
        : $amount;
    $commencement = now()->toDateString();

    return view('bond-agreement', [
        'agreement' => [
            'package_name' => $package['name'],
            'amount' => '$'.number_format($amountInUsd, 2),
            'daily_interest_rate' => number_format($package['daily_interest_rate'], 2).'%',
            'daily_interest_income' => '$'.number_format($amountInUsd * ((float) $package['daily_interest_rate'] / 100), 2),
            'duration_days' => $package['duration_days'].' days',
            'commencement_date' => $commencement,
            'maturity_date' => now()->addDays($package['duration_days'])->toDateString(),
            'bondholder' => $request->user()->name ?: $request->user()->email,
            'signature_name' => null,
            'reference' => 'PREVIEW',
            'contract_number' => 'PREVIEW',
        ],
        'isSample' => false,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.agreement.preview');

Route::get('/invest/agreement/sign', function (Request $request) {
    $investment = null;
    if ($request->filled('investment_id')) {
        $investment = $request->user()->investments()
            ->whereKey($request->integer('investment_id'))
            ->firstOrFail();
        abort_unless(
            ! $investment->cancelled_at
                && ! $investment->agreement_signed_at
                && in_array($investment->status, ['pending', 'approved'], true)
                && in_array($investment->payment_method, ['bank_transfer', 'e_wallet', 'account_balance', 'admin_transfer'], true),
            404
        );

        $packageKey = $investment->package_key;
        $currency = 'USD';
        $amount = (float) $investment->amount;
        $paymentMethod = $investment->payment_method;
    } else {
        $packageKey = (string) $request->query('package');
        $currency = $request->query('currency', 'USD');
        $amount = (float) $request->query('amount', 0);
        $paymentMethod = (string) $request->query('payment_method', 'bank_transfer');
        abort_unless(in_array($currency, ['USD', 'PHP'], true), 422);
        abort_unless(in_array($paymentMethod, ['bank_transfer', 'e_wallet', 'account_balance', 'crypto'], true), 422);
    }

    $package = InvestmentPackages::find($packageKey);
    abort_unless($package, 404);

    $contractPath = base_path('LuLu-Contract-Form.html');
    abort_unless(is_file($contractPath), 500, 'The bond agreement form is unavailable.');

    $contractHtml = file_get_contents($contractPath);
    abort_if($contractHtml === false, 500, 'The bond agreement form could not be read.');

    $integration = view('contracts.investment-signing', [
        'purchase' => [
            'package' => $packageKey,
            'package_name' => $package['name'],
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $paymentMethod,
            'investment_id' => $investment?->id,
        ],
        'bondholder' => $request->user()->name ?: $request->user()->email,
    ])->render();

    abort_unless(str_contains($contractHtml, '</body>'), 500, 'The bond agreement form is incomplete.');

    return response(str_replace('</body>', $integration.'</body>', $contractHtml), 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.agreement.sign');

Route::post('/invest/payment/complete', [InvestmentController::class, 'confirmPayment'])
    ->middleware(['auth', RestrictUserAccess::class, 'throttle:10,1'])
    ->name('invest.payment.complete');
Route::get('/investments/{investment}/receipt', [InvestmentController::class, 'receipt'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('investments.receipt');

Route::get('/invest/payment/{provider}', function (string $provider) {
    $providers = [
        'landbank' => [
            'name' => 'LandBank',
            'logo' => 'Landbank.svg',
            'qr' => 'LandBank.png',
            'launch_url' => 'https://www.landbank.com/',
            'background' => '#006b3f',
            'accent' => '#f5c542',
        ],
        'bpi' => [
            'name' => 'BPI',
            'logo' => 'Bpi.svg',
            'qr' => 'bpi.png',
            'launch_url' => 'https://online.bpi.com.ph/',
            'background' => '#005baa',
            'accent' => '#d71920',
        ],
        'palawanpay' => [
            'name' => 'Palawan Pay',
            'logo' => null,
            'qr' => 'PalawanPay.png',
            'launch_url' => 'https://www.palawanpay.com/',
            'background' => '#078b4f',
            'accent' => '#f4c542',
            'payment_method' => 'e_wallet',
        ],
    ];
    abort_unless(isset($providers[$provider]), 404);

    $packageKey = (string) request()->query('package');
    $package = InvestmentPackages::find($packageKey);
    abort_unless($package, 404);

    $amount = (float) request()->query('amount', $package['price']);
    $currency = request()->query('currency', 'USD');
    abort_unless(in_array($currency, ['USD', 'PHP'], true), 404);

    return view('invest-payment', [
        'providerKey' => $provider,
        'provider' => $providers[$provider],
        'paymentProviders' => [
            'banks' => array_intersect_key($providers, array_flip(['bpi', 'landbank'])),
            'wallets' => array_intersect_key($providers, array_flip(['palawanpay'])),
        ],
        'packageKey' => $packageKey,
        'package' => $package,
        'amount' => $amount,
        'currency' => $currency,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.payment');

// User actions (authenticated)
Route::get('/send', function () {
    $user = Auth::user();
    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user->refresh();

    $availableBalance = (float) $user->balance;
    $recentTransfers = AccountActivity::query()
        ->where('user_id', $user->id)
        ->whereIn('type', ['funds_sent', 'funds_received'])
        ->latest('occurred_at')
        ->limit(5)
        ->get()
        ->each(function (AccountActivity $activity) use ($user): void {
            $currency = CurrencyDisplay::preferredFor($user);
            $rate = $currency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : 1.0;
            $activity->description = str_replace(
                '$'.number_format((float) $activity->amount, 2),
                CurrencyDisplay::formatUsd((float) $activity->amount, $currency, $rate),
                $activity->description
            );
        });

    return view('send', [
        'availableBalance' => $availableBalance,
        'recentTransfers' => $recentTransfers,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('send');

Route::get('/transfers/{accountActivity}/receipt', function (Request $request, AccountActivity $accountActivity) {
    abort_unless(
        $accountActivity->user_id === $request->user()->id
            && in_array($accountActivity->type, ['funds_sent', 'funds_received'], true)
            && $accountActivity->transfer_reference,
        404
    );

    $otherActivity = AccountActivity::query()
        ->with('user')
        ->where('transfer_reference', $accountActivity->transfer_reference)
        ->where('id', '!=', $accountActivity->id)
        ->whereIn('type', ['funds_sent', 'funds_received'])
        ->firstOrFail();

    abort_unless(
        $otherActivity->user
            && $otherActivity->type !== $accountActivity->type
            && (float) $otherActivity->amount === (float) $accountActivity->amount,
        404
    );

    $senderActivity = $accountActivity->type === 'funds_sent' ? $accountActivity : $otherActivity;
    $recipientActivity = $accountActivity->type === 'funds_received' ? $accountActivity : $otherActivity;

    return view('transfer-receipt', [
        'activity' => $accountActivity,
        'sender' => $senderActivity->user,
        'recipient' => $recipientActivity->user,
        'isSender' => $accountActivity->type === 'funds_sent',
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('transfers.receipt');

Route::post('/send', function (Request $request) {
    $data = $request->validate([
        'recipient' => ['required', 'string', 'max:255'],
        'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999999.99'],
        'message' => ['nullable', 'string', 'max:120'],
    ]);

    $recipientIdentifier = trim($data['recipient']);
    $matchingEmails = User::query()
        ->whereRaw('LOWER(email) = ?', [mb_strtolower($recipientIdentifier)])
        ->limit(2)
        ->get();

    if ($matchingEmails->count() > 1) {
        throw ValidationException::withMessages([
            'recipient' => 'More than one user has that email address. Contact support before sending funds.',
        ]);
    }

    $recipient = $matchingEmails->first();

    if (! $recipient) {
        $matchingRecipients = User::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($recipientIdentifier)])
            ->limit(2)
            ->get();

        if ($matchingRecipients->count() > 1) {
            throw ValidationException::withMessages([
                'recipient' => 'More than one user has that full name. Enter the recipient email address instead.',
            ]);
        }

        $recipient = $matchingRecipients->first();
    }

    if (! $recipient) {
        throw ValidationException::withMessages([
            'recipient' => 'No user was found with that email address or full name.',
        ]);
    }

    if ($recipient->is($request->user())) {
        throw ValidationException::withMessages([
            'recipient' => 'You cannot send funds to your own account.',
        ]);
    }

    $currency = CurrencyDisplay::preferredFor($request->user());
    $rate = $currency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : 1.0;
    $amount = CurrencyDisplay::toUsd((float) $data['amount'], $currency, $rate);
    if ($amount < 0.01) {
        throw ValidationException::withMessages([
            'amount' => 'Enter an amount of at least '.CurrencyDisplay::formatUsd(0.01, $currency, $rate).'.',
        ]);
    }
    DailyInterestAccrualService::accrueDueInterestForUser($request->user());

    $transferReference = (string) Str::uuid();
    $senderActivity = DB::transaction(function () use ($request, $recipient, $amount, $data, $transferReference): AccountActivity {
        $accounts = User::query()
            ->whereIn('id', [$request->user()->id, $recipient->id])
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $sender = $accounts->get($request->user()->id);
        $lockedRecipient = $accounts->get($recipient->id);

        if (! $sender || ! $lockedRecipient) {
            throw ValidationException::withMessages([
                'recipient' => 'The recipient account is no longer available.',
            ]);
        }

        if ((float) $sender->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient available balance for this transfer.',
            ]);
        }

        $sender->balance = round((float) $sender->balance - $amount, 2);
        $lockedRecipient->balance = round((float) $lockedRecipient->balance + $amount, 2);
        $sender->save();
        $lockedRecipient->save();

        $memo = trim((string) ($data['message'] ?? ''));
        $memoSuffix = $memo === '' ? '' : ' Message: '.$memo;
        $occurredAt = now();

        $senderActivity = AccountActivity::create([
            'user_id' => $sender->id,
            'type' => 'funds_sent',
            'title' => 'Funds sent',
            'description' => 'You sent funds to '.($lockedRecipient->name ?: $lockedRecipient->email).'.'.$memoSuffix,
            'amount' => $amount,
            'occurred_at' => $occurredAt,
            'transfer_reference' => $transferReference,
        ]);

        AccountActivity::create([
            'user_id' => $lockedRecipient->id,
            'type' => 'funds_received',
            'title' => 'Funds received',
            'description' => 'You received funds from '.($sender->name ?: $sender->email).'.'.$memoSuffix,
            'amount' => $amount,
            'occurred_at' => $occurredAt,
            'transfer_reference' => $transferReference,
        ]);

        return $senderActivity;
    });

    return redirect()->route('transfers.receipt', $senderActivity)
        ->with('status', 'Sent '.CurrencyDisplay::formatUsd($amount, $currency, $rate).' to '.($recipient->name ?: $recipient->email).'.');
})->middleware(['auth', RestrictUserAccess::class])->name('send.transfer');

Route::get('/withdraw', function () {
    $user = Auth::user();
    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user->refresh();

    $availableBalance = (float) $user->balance;

    $recentWithdrawals = Withdrawal::where('user_id', $user->id)
        ->notSignupBonus()
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    return view('withdraw', [
        'availableBalance' => $availableBalance,
        'recentWithdrawals' => $recentWithdrawals,
        'withdrawalProviders' => [
            'e_wallet' => ['GCash', 'Maya', 'GrabPay', 'ShopeePay', 'Coins.ph'],
            'bank' => ['BDO Unibank', 'BPI', 'Metrobank', 'LandBank', 'UnionBank'],
        ],
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('withdraw');

Route::get('/withdrawals/{withdrawal}/receipt', function (Request $request, Withdrawal $withdrawal) {
    abort_unless($withdrawal->user_id === $request->user()->id, 404);

    return view('withdrawal-receipt', ['withdrawal' => $withdrawal]);
})->middleware(['auth', RestrictUserAccess::class])->name('withdrawals.receipt');

Route::post('/withdrawals', function (Request $request) {
    $providers = [
        'e_wallet' => ['GCash', 'Maya', 'GrabPay', 'ShopeePay', 'Coins.ph'],
        'bank' => ['BDO Unibank', 'BPI', 'Metrobank', 'LandBank', 'UnionBank'],
    ];
    $accountPatterns = [
        'GCash' => '/^(09|\+639)\d{9}$/',
        'Maya' => '/^(09|\+639)\d{9}$/',
        'GrabPay' => '/^(09|\+639)\d{9}$/',
        'ShopeePay' => '/^(09|\+639)\d{9}$/',
        'Coins.ph' => '/^(09|\+639)\d{9}$/',
        'BDO Unibank' => '/^\d{10}$/',
        'BPI' => '/^\d{10}$/',
        'Metrobank' => '/^\d{13}$/',
        'LandBank' => '/^\d{10}$/',
        'UnionBank' => '/^\d{12}$/',
    ];

    $data = $request->validate([
        'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999999.99'],
        // account_type is optional for backwards compatibility with older clients.
        'account_type' => ['nullable', 'in:bank,e_wallet'],
        'bank_name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) use ($request, $providers) {
                $type = $request->input('account_type');
                if ($type && ! in_array($value, $providers[$type], true)) {
                    $fail('Please select a valid withdrawal provider.');
                }
            },
        ],
        'account_number' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) use ($request, $accountPatterns) {
                $provider = $request->input('bank_name');
                $pattern = $accountPatterns[$provider] ?? null;
                // Keep accepting legacy manually-entered bank details when no account type was sent.
                if ($request->filled('account_type') && $pattern && ! preg_match($pattern, $value)) {
                    $fail('Enter a valid account number for the selected provider.');
                }
            },
        ],
        'account_holder' => ['required', 'string', 'max:255'],
    ]);

    $user = $request->user();
    $currency = CurrencyDisplay::preferredFor($user);
    $rate = $currency === 'PHP' ? CurrencyRateService::latestUsdToPhp() : 1.0;
    $withdrawalAmount = CurrencyDisplay::toUsd((float) $data['amount'], $currency, $rate);

    if ($withdrawalAmount < 20 || $withdrawalAmount > 500) {
        $minimum = CurrencyDisplay::formatUsd(20, $currency, $rate);
        $maximum = CurrencyDisplay::formatUsd(500, $currency, $rate);
        throw ValidationException::withMessages([
            'amount' => "Withdrawal amount must be between {$minimum} and {$maximum}.",
        ]);
    }

    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user->refresh();

    $availableBalance = (float) $user->balance;

    if ($availableBalance < $withdrawalAmount) {
        throw ValidationException::withMessages([
            'amount' => 'Insufficient balance for this withdrawal request.',
        ]);
    }

    $withdrawal = DB::transaction(function () use ($user, $data, $withdrawalAmount) {
        $user->update([
            'bank_name' => $data['bank_name'],
            'bank_account_number' => $data['account_number'],
            'bank_account_holder' => $data['account_holder'],
            'withdrawal_account_type' => $data['account_type'] ?? ($user->withdrawal_account_type ?: 'bank'),
        ]);

        $processingFee = round($withdrawalAmount * 0.05, 2);
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $withdrawalAmount,
            'transaction_reference' => 'WD-'.strtoupper((string) Str::uuid()),
            'processing_fee' => $processingFee,
            'total_withdrawn' => round($withdrawalAmount - $processingFee, 2),
            'payment_method' => ($data['account_type'] ?? $user->withdrawal_account_type) === 'e_wallet'
                ? 'mobile_money'
                : 'bank_transfer',
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_holder' => $data['account_holder'],
            'status' => 'pending',
        ]);

        $user->balance = max(0, ($user->balance ?? 0) - $withdrawalAmount);
        $user->save();

        return $withdrawal;
    });

    try {
        Mail::to($user->email)->send(new WithdrawalConfirmation($withdrawal->load('user')));
    } catch (Throwable $e) {
        Log::warning('Failed to send withdrawal confirmation email', [
            'withdrawal_id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'error' => $e->getMessage(),
        ]);
    }

    return redirect()->route('withdrawals.receipt', $withdrawal);
})->middleware(['auth', RestrictUserAccess::class])->name('withdrawals.store');

Route::post('/withdrawal-account', function (Request $request) {
    $providers = [
        'e_wallet' => ['GCash', 'Maya', 'GrabPay', 'ShopeePay', 'Coins.ph'],
        'bank' => ['BDO Unibank', 'BPI', 'Metrobank', 'LandBank', 'UnionBank'],
    ];
    $patterns = [
        'GCash' => '/^(09|\+639)\d{9}$/',
        'Maya' => '/^(09|\+639)\d{9}$/',
        'GrabPay' => '/^(09|\+639)\d{9}$/',
        'ShopeePay' => '/^(09|\+639)\d{9}$/',
        'Coins.ph' => '/^(09|\+639)\d{9}$/',
        'BDO Unibank' => '/^\d{10}$/',
        'BPI' => '/^\d{10}$/',
        'Metrobank' => '/^\d{13}$/',
        'LandBank' => '/^\d{10}$/',
        'UnionBank' => '/^\d{12}$/',
    ];

    $data = $request->validate([
        'account_type' => ['required', 'in:bank,e_wallet'],
        'bank_name' => ['required', 'string', 'in:'.implode(',', array_merge($providers['bank'], $providers['e_wallet']))],
        'account_number' => ['required', 'string', 'max:255'],
        'account_holder' => ['required', 'string', 'max:255'],
    ]);

    if (! in_array($data['bank_name'], $providers[$data['account_type']], true)
        || ! preg_match($patterns[$data['bank_name']], $data['account_number'])) {
        throw ValidationException::withMessages([
            'account_number' => 'Enter valid details for the selected withdrawal provider.',
        ]);
    }

    $request->user()->update([
        'bank_name' => $data['bank_name'],
        'bank_account_number' => $data['account_number'],
        'bank_account_holder' => $data['account_holder'],
        'withdrawal_account_type' => $data['account_type'],
    ]);

    return redirect()->route('withdraw')->with('status', 'Withdrawal account saved successfully.');
})->middleware(['auth', RestrictUserAccess::class])->name('withdrawal-account.store');

Route::get('/history', function () {
    $user = Auth::user();
    DailyInterestAccrualService::accrueDueInterestForUser($user);

    $investments = Investment::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $withdrawals = Withdrawal::where('user_id', $user->id)
        ->notSignupBonus()
        ->orderByDesc('created_at')
        ->get();

    $approvedInvestments = $investments->where('status', 'approved');
    $dailyInterest = $approvedInvestments->sum(fn ($investment) => $investment->creditedInterest());
    $dailyInterestEntries = collect();

    foreach ($approvedInvestments as $investment) {
        $creditedDays = min(
            (int) ($investment->interest_days_credited ?? 0),
            (int) $investment->duration_days
        );
        $firstInterestDate = $investment->effectiveStartDate()->copy()->startOfDay()->addDay();

        for ($day = 0; $day < $creditedDays; $day++) {
            $dailyInterestEntries->push([
                'date' => $firstInterestDate->copy()->addDays($day),
                'investment' => $investment,
                'amount' => $investment->dailyInterestAmount(),
            ]);
        }
    }

    $dailyInterestEntries = $dailyInterestEntries
        ->sortByDesc(fn (array $entry) => $entry['date'])
        ->values();

    return view('history', [
        'investments' => $investments,
        'withdrawals' => $withdrawals,
        'dailyInterest' => $dailyInterest,
        'dailyInterestEntries' => $dailyInterestEntries,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('history');

Route::get('/referrals', function () {
    return view('referrals');
})->middleware(['auth', RestrictUserAccess::class])->name('referrals');

Route::get('/cards', function () {
    return view('cards');
})->middleware(['auth', RestrictUserAccess::class])->name('cards');

Route::get('/loan', function () {
    return view('loan');
})->middleware(['auth', RestrictUserAccess::class])->name('loan');

Route::get('/profile', function () {
    return view('profile');
})->middleware(['auth', RestrictUserAccess::class])->name('profile');

Route::get('/lulu-home-screen.html', function () {
    $guidePath = base_path('lulu-home-screen.html');
    abort_unless(is_file($guidePath), 404);

    return response()->file($guidePath, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('home-screen.guide');

Route::get('/profile/verification', [IdentityVerificationController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('profile.verification');
Route::post('/profile/verification', [IdentityVerificationController::class, 'submit'])
    ->middleware(['auth', RestrictUserAccess::class, 'throttle:3,10'])
    ->name('profile.verification.submit');

Route::get('/profile/edit', function () {
    return view('profile-edit');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.edit');

Route::post('/profile/edit', function (Request $request) {
    $user = $request->user();
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        'region' => ['nullable', 'string', 'max:255'],
    ]);

    $user->update($validated);

    return redirect()->route('profile.edit')->with('status', 'Profile updated successfully.');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.update');

Route::get('/profile/password', function () {
    return view('change-password');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.password');
Route::post('/profile/password', [AuthController::class, 'changePassword'])
    ->middleware(['auth', RestrictUserAccess::class, 'throttle:5,1'])
    ->name('profile.password.update');

Route::get('/profile/notifications', function () {
    $preferences = array_merge([
        'email_notifications' => true,
        'account_alerts' => true,
        'marketing_updates' => false,
    ], Auth::user()->notification_preferences ?? []);

    return view('notification-settings', [
        'notificationPreferences' => $preferences,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('profile.notifications');

Route::post('/profile/notifications', function (Request $request) {
    $validated = $request->validate([
        'preferences' => ['required', 'array'],
        'preferences.email_notifications' => ['required', 'boolean'],
        'preferences.account_alerts' => ['required', 'boolean'],
        'preferences.marketing_updates' => ['required', 'boolean'],
    ]);

    $user = $request->user();
    $user->notification_preferences = collect($validated['preferences'])
        ->map(fn ($enabled) => (bool) $enabled)
        ->all();
    $user->save();

    return redirect()->route('profile.notifications')->with('status', 'Notification preferences saved.');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.notifications.update');

Route::post('/push-subscriptions', function (Request $request) {
    $validated = $request->validate([
        'endpoint' => ['required', 'string', 'url', 'regex:/\Ahttps:\/\/(?:fcm\.googleapis\.com|updates\.push\.services\.mozilla\.com|web\.push\.apple\.com|[a-z0-9-]+\.notify\.windows\.com)\//i', 'max:2048'],
        'keys.p256dh' => ['required', 'string', 'max:255'],
        'keys.auth' => ['required', 'string', 'max:255'],
    ]);

    PushSubscription::query()->updateOrCreate(
        ['endpoint_hash' => hash('sha256', $validated['endpoint'])],
        [
            'user_id' => $request->user()->id,
            'endpoint' => $validated['endpoint'],
            'public_key' => $validated['keys']['p256dh'],
            'auth_token' => $validated['keys']['auth'],
        ]
    );

    return response()->json(['message' => 'Daily interest notifications enabled.']);
})->middleware(['auth', RestrictUserAccess::class, 'throttle:10,1'])->name('push-subscriptions.store');

Route::delete('/push-subscriptions', function (Request $request) {
    $validated = $request->validate([
        'endpoint' => ['required', 'string', 'url', 'regex:/\Ahttps:\/\/(?:fcm\.googleapis\.com|updates\.push\.services\.mozilla\.com|web\.push\.apple\.com|[a-z0-9-]+\.notify\.windows\.com)\//i', 'max:2048'],
    ]);

    PushSubscription::query()
        ->where('user_id', $request->user()->id)
        ->where('endpoint_hash', hash('sha256', $validated['endpoint']))
        ->delete();

    return response()->json(['message' => 'Daily interest notifications disabled.']);
})->middleware(['auth', RestrictUserAccess::class, 'throttle:10,1'])->name('push-subscriptions.destroy');

Route::post('/investments', [InvestmentController::class, 'store'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('investments.store');

Route::get('/admin/dashboard', function () {
    abort_unless(Auth::user()?->is_admin, 403);

    return app(UserManagementController::class)->index(request());
})->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])->name('admin.dashboard');

Route::get('/admin/dashboard/purchase-notifications', [UserManagementController::class, 'purchaseNotifications'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.dashboard.purchase-notifications');

Route::get('/admin/dashboard/funds-notifications', [UserManagementController::class, 'fundsNotifications'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.dashboard.funds-notifications');

Route::get('/admin/franchises', [AdminFranchiseApplicationController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.franchises');

Route::get('/admin/franchises/{franchiseApplication}', [AdminFranchiseApplicationController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.franchises.show');

Route::get('/admin/user-activity', function (Request $request) {
    abort_unless(Auth::user()?->is_admin, 403);

    return app(UserManagementController::class)->activity($request);
})->middleware(['auth', RestrictUserAccess::class])->name('admin.user-activity');

Route::get('/admin/user-search-suggestions', [UserManagementController::class, 'searchSuggestions'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.user-search-suggestions');

Route::get('/admin/identity-verifications', [AdminIdentityVerificationController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.identity-verifications');
Route::get('/admin/identity-verifications/{verification}/documents/{side}', [AdminIdentityVerificationController::class, 'document'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.identity-verifications.document');
Route::post('/admin/identity-verifications/{verification}/approve', [AdminIdentityVerificationController::class, 'approve'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.identity-verifications.approve');
Route::post('/admin/identity-verifications/{verification}/reject', [AdminIdentityVerificationController::class, 'reject'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.identity-verifications.reject');

Route::post('/admin/users/{user}/verify', [AdminIdentityVerificationController::class, 'markUserVerified'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.users.verify');

Route::get('/admin/users/{user}', function (User $user) {
    abort_unless(Auth::user()?->is_admin, 403);

    return app(UserManagementController::class)->show($user);
})->middleware(['auth', RestrictUserAccess::class])->name('admin.users.show');

Route::delete('/admin/users/{user}/investments/{investment}', [UserManagementController::class, 'destroyInvestment'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.investments.destroy');

Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.destroy');

Route::post('/admin/users/{user}/restrict', [UserManagementController::class, 'restrict'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.restrict');

Route::post('/admin/users/{user}/block-ip', [UserManagementController::class, 'blockIp'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.block-ip');

Route::post('/admin/users/{user}/temporary-password', [UserManagementController::class, 'createTemporaryPassword'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.temporary-password');

Route::post('/admin/account-invitations', [AccountInvitationController::class, 'store'])
    ->middleware(['auth', RestrictUserAccess::class, RequireInvestorPin::class])
    ->name('admin.account-invitations.store');

Route::delete('/admin/blocked-ips/{blockedIp}', [UserManagementController::class, 'unblockIp'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.blocked-ips.destroy');

Route::post('/admin/backup', [UserManagementController::class, 'backup'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.backup');

Route::get('/admin/investments', [InvestmentApprovalController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments');

Route::get('/admin/investments/{investment}', [InvestmentApprovalController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments.show');

Route::get('/admin/client-contracts', [ClientContractController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.contracts');
Route::get('/admin/client-contracts/{investment}', [ClientContractController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.contracts.show');

Route::post('/admin/investments/{investment}/approve', [InvestmentApprovalController::class, 'approve'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments.approve');

Route::get('/admin/withdrawals', [WithdrawalController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.withdrawals');

Route::get('/admin/withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.withdrawals.show');

Route::post('/admin/withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.withdrawals.approve');

Route::post('/admin/withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.withdrawals.reject');

Route::post('/admin/investments/{investment}/reject', [InvestmentApprovalController::class, 'reject'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments.reject');

Route::post('/admin/send-package', [UserManagementController::class, 'sendPackage'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.send-package');

Route::post('/admin/package-slots', [UserManagementController::class, 'updatePackageSlots'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.package-slots.update');

Route::post('/admin/send-funds', [UserManagementController::class, 'sendFunds'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.send-funds');

Route::post('/admin/send-promotional-email', [UserManagementController::class, 'sendPromotionalEmail'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.send-promotional-email');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
