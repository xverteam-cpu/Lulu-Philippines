<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\InvestmentApprovalController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\InvestmentController;
use App\Http\Middleware\RestrictUserAccess;
use App\Models\Investment;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\CurrencyRateService;
use App\Support\DailyInterestAccrualService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('public.home');
})->name('public.home');

Route::get('/investors', function () {
    return view('public.investors');
})->name('investors');

Route::get('/home', function () {
    return view('lotteria');
})->name('home');

Route::get('/order', function () {
    return redirect()->route('investors');
})->name('order');

Route::get('/login', function () {
    return redirect()->route('investors');
})->name('login');

Route::get('/login/google', function () {
    return redirect()->route('saml2_login', ['idpName' => 'google']);
})->name('login.google');

Route::get('/debug/test-email', function (Illuminate\Http\Request $request) {
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

    Mail::to($email)->send(new App\Mail\WithdrawalConfirmation($withdrawal));

    return redirect()->route('withdraw')->with('status', 'Test withdrawal email sent.');
})->name('debug.test-email');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register-partner', [AuthController::class, 'register'])->name('register.partner');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/signup', function () {
    return view('signup', ['referral' => request('ref')]);
})->name('signup');

Route::get('/franchising', function () {
    return view('franchising', ['referral' => request('ref')]);
})->name('franchising');

Route::get('/unavailable', function () {
    return view('unavailable');
})->name('unavailable');

Route::get('/dashboard', function () {
    if (Auth::user()?->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    $user = Auth::user();
    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user = $user->fresh();  // Refresh from database to get updated balance

    $recentInvestments = Investment::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    $recentWithdrawals = Withdrawal::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    $dailyInterest = $user->investments()
        ->where('status', 'approved')
        ->get()
        ->sum(fn ($investment) => $investment->dailyInterestAmount());

    if (! is_numeric($dailyInterest)) {
        $dailyInterest = 0;
    }

    $notifications = collect();

    if ($dailyInterest > 0) {
        $notifications->push([
            'id' => 'daily-interest',
            'title' => 'Daily interest credited',
            'description' => '$'.number_format($dailyInterest, 2).' added to available balance.',
            'time' => now(),
        ]);
    }

    foreach ($recentInvestments as $investment) {
        $description = match ($investment->payment_method) {
            'admin_transfer' => 'Package sent by admin: '.$investment->package_name.'.',
            'account_balance' => 'Investment purchased from your balance: '.$investment->package_name.'.',
            'bank_transfer' => 'Investment submitted and pending approval: '.$investment->package_name.'.',
            default => 'Investment activity: '.$investment->package_name.'.',
        };

        $notifications->push([
            'id' => 'investment-'.$investment->id,
            'title' => 'Investment update',
            'description' => $description,
            'time' => $investment->created_at,
        ]);
    }

    foreach ($recentWithdrawals as $withdrawal) {
        $notifications->push([
            'id' => 'withdrawal-'.$withdrawal->id,
            'title' => 'Withdrawal request',
            'description' => '₱'.number_format($withdrawal->amount, 2).' '.$withdrawal->status.'.',
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
        'notifications' => $notifications,
        'notificationsRead' => $notificationsRead,
        'unreadCount' => $unreadCount,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('dashboard');

Route::post('/notifications/read-all', function (Illuminate\Http\Request $request) {
    $data = $request->validate([
        'ids' => ['required', 'array'],
        'ids.*' => ['string'],
    ]);

    $request->user()->markNotificationsRead($data['ids']);

    return response()->json(['status' => 'success']);
})->middleware(['auth'])->name('notifications.read_all');

Route::get('/rewards', function () {
    $user = Auth::user();

    return view('rewards', [
        'user' => $user,
        'signupBonusClaimed' => ! empty($user->signup_bonus_claimed_at),
        'signupBonusAmount' => 5,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('rewards');

Route::post('/rewards/claim-signup-bonus', function (Illuminate\Http\Request $request) {
    $user = $request->user();

    if (! empty($user->signup_bonus_claimed_at)) {
        return redirect()->route('rewards')->with('status', 'You already claimed your $5 sign up bonus.');
    }

    DB::transaction(function () use ($user): void {
        $user->forceFill([
            'balance' => (float) ($user->balance ?? 0) + 5,
            'signup_bonus_claimed_at' => now(),
        ])->save();

        App\Models\Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 5,
            'payment_method' => 'account_balance',
            'bank_name' => 'Welcome Bonus',
            'account_number' => 'signup-bonus',
            'account_holder' => 'Sign Up Bonus',
            'status' => 'approved',
        ]);
    });

    return redirect()->route('rewards')->with('status', 'Your $5 sign up bonus has been added to your available balance.');
})->middleware(['auth', RestrictUserAccess::class])->name('rewards.claim-signup-bonus');

// Deposit page for buying shares
Route::get('/deposit', function () {
    return view('deposit');
})->middleware(['auth', RestrictUserAccess::class])->name('deposit');

Route::get('/invest', function () {
    $meta = CurrencyRateService::latestUsdToPhpWithMeta();
    $user = Auth::user();
    $totalInvestment = $user ? (float) $user->investments()->where('status', 'approved')->sum('amount') : 0;
    $availableBalance = 0;
    if ($user) {
        DailyInterestAccrualService::accrueDueInterestForUser($user);
        $user->refresh();
        $availableBalance = (float) $user->balance + $user->investments()->latest()->get()->sum(fn ($investment) => $investment->earnedInterest());
    }

    return view('invest', [
        'phpRate' => $meta['rate'],
        'phpRateUpdatedAt' => $meta['updated_at'],
        'packageSlots' => App\Support\InvestmentPackages::currentSlots(),
        'totalInvestment' => $totalInvestment,
        'availableBalance' => $availableBalance,
    ]);
})->name('invest');

Route::get('/invest/purchase/{package}', function (string $package) {
    $selectedPackage = App\Support\InvestmentPackages::find($package);
    abort_unless($selectedPackage, 404);

    $meta = CurrencyRateService::latestUsdToPhpWithMeta();

    return view('invest-purchase', [
        'packageKey' => $package,
        'package' => $selectedPackage,
        'phpRate' => $meta['rate'],
        'phpRateUpdatedAt' => $meta['updated_at'],
        'requiresAgreement' => true,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.purchase');

Route::get('/invest/agreement/sample', function () {
    return view('bond-agreement', [
        'agreement' => [
            'package_name' => '________________________________________',
            'amount' => '________________________________________',
            'daily_interest_rate' => '________________________________________',
            'daily_interest_income' => '________________________________________',
            'duration_days' => '________________________________________',
            'commencement_date' => '______________________________',
            'maturity_date' => '________________________________________',
            'bondholder' => '________________________________________',
            'civil_status' => '__________________',
            'residence' => '________________________________________________________________________________________',
            'reference' => '________________________________________',
            'contract_number' => '________________________________________',
        ],
        'isSample' => true,
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

Route::get('/invest/agreement/preview', function (Illuminate\Http\Request $request) {
    $package = App\Support\InvestmentPackages::find((string) $request->query('package'));
    abort_unless($package, 404);

    $currency = $request->query('currency', 'USD');
    abort_unless(in_array($currency, ['USD', 'PHP'], true), 422);
    $amount = (float) $request->query('amount', $package['price']);
    $amountInUsd = $currency === 'PHP'
        ? $amount / (float) config('currency.usd_to_php', 61.31)
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

Route::get('/invest/agreement/sign', function (Illuminate\Http\Request $request) {
    $packageKey = (string) $request->query('package');
    $package = App\Support\InvestmentPackages::find($packageKey);
    abort_unless($package, 404);

    $currency = $request->query('currency', 'USD');
    abort_unless(in_array($currency, ['USD', 'PHP'], true), 422);
    $amount = (float) $request->query('amount', $package['price']);
    $amountInUsd = $currency === 'PHP'
        ? $amount / (float) config('currency.usd_to_php', 61.31)
        : $amount;
    $paymentMethod = (string) $request->query('payment_method', 'bank_transfer');
    abort_unless(in_array($paymentMethod, ['bank_transfer', 'e_wallet', 'account_balance', 'crypto'], true), 422);
    $commencement = now()->toDateString();

    return view('bond-agreement', [
        'agreement' => [
            'package_name' => $package['name'],
            'amount' => '$'.number_format($amountInUsd, 2),
            'duration_days' => $package['duration_days'].' days',
            'daily_interest_rate' => number_format($package['daily_interest_rate'], 2).'%',
            'daily_interest_income' => '$'.number_format($amountInUsd * ((float) $package['daily_interest_rate'] / 100), 2),
            'commencement_date' => $commencement,
            'maturity_date' => now()->addDays($package['duration_days'])->toDateString(),
            'bondholder' => $request->user()->name ?: $request->user()->email,
            'signature_name' => null,
            'reference' => 'PENDING',
            'contract_number' => 'PENDING',
        ],
        'isSample' => false,
        'isSigning' => true,
        'purchase' => [
            'package' => $packageKey,
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $paymentMethod,
        ],
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.agreement.sign');

Route::get('/invest/payment/{provider}', function (string $provider) {
    $providers = [
        'landbank' => [
            'name' => 'Landbank',
            'logo' => 'Landbank.svg',
            'qr' => 'LandbankQR.png',
            'launch_url' => 'https://www.landbank.com/',
            'background' => '#006b3f',
            'accent' => '#f5c542',
        ],
        'bpi' => [
            'name' => 'BPI',
            'logo' => 'Bpi.svg',
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://online.bpi.com.ph/',
            'background' => '#005baa',
            'accent' => '#d71920',
        ],
        'bdo' => [
            'name' => 'BDO',
            'logo' => 'BDO.svg',
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://online.bdo.com.ph/',
            'background' => '#003b70',
            'accent' => '#f58220',
        ],
        'unionbank' => [
            'name' => 'UnionBank',
            'logo' => 'UnionBank.svg',
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://online.unionbankph.com/',
            'background' => '#f36f21',
            'accent' => '#ffb81c',
        ],
        'gcash' => [
            'name' => 'GCash',
            'logo' => null,
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://www.gcash.com/',
            'background' => '#007cff',
            'accent' => '#00a9e8',
            'payment_method' => 'e_wallet',
        ],
        'maya' => [
            'name' => 'Maya',
            'logo' => null,
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://www.maya.ph/',
            'background' => '#00a86b',
            'accent' => '#7bdcb5',
            'payment_method' => 'e_wallet',
        ],
        'grabpay' => [
            'name' => 'GrabPay',
            'logo' => null,
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://www.grab.com/ph/pay/',
            'background' => '#00b14f',
            'accent' => '#b9f227',
            'payment_method' => 'e_wallet',
        ],
        'shopeepay' => [
            'name' => 'ShopeePay',
            'logo' => null,
            'qr' => 'BPIQR.png',
            'launch_url' => 'https://shopee.ph/m/shopeepay',
            'background' => '#ee4d2d',
            'accent' => '#ffb300',
            'payment_method' => 'e_wallet',
        ],
    ];
    abort_unless(isset($providers[$provider]), 404);

    $packageKey = (string) request()->query('package');
    $package = App\Support\InvestmentPackages::find($packageKey);
    abort_unless($package, 404);

    $amount = (float) request()->query('amount', $package['price']);
    $currency = request()->query('currency', 'USD');
    abort_unless(in_array($currency, ['USD', 'PHP'], true), 404);

    return view('invest-payment', [
        'providerKey' => $provider,
        'provider' => $providers[$provider],
        'packageKey' => $packageKey,
        'package' => $package,
        'amount' => $amount,
        'currency' => $currency,
    ]);
})->middleware(['auth', RestrictUserAccess::class])->name('invest.payment');

// User actions (authenticated)
Route::get('/send', function () {
    return view('send');
})->middleware(['auth', RestrictUserAccess::class])->name('send');

Route::get('/withdraw', function () {
    $user = Auth::user();
    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user->refresh();

    $investments = $user->investments()->latest()->get();
    $earnedIncome = $investments->sum(fn ($investment) => $investment->earnedInterest());
    $availableBalance = (float) $user->balance + $earnedIncome;

    $recentWithdrawals = Withdrawal::where('user_id', $user->id)
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

Route::post('/withdrawals', function (Illuminate\Http\Request $request) {
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
        'amount' => ['required', 'numeric', 'min:20', 'max:500'],
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

    DailyInterestAccrualService::accrueDueInterestForUser($user);
    $user->refresh();

    $investments = $user->investments()->latest()->get();
    $availableBalance = (float) $user->balance + $investments->sum(fn ($investment) => $investment->earnedInterest());

    if ($availableBalance < (float) $data['amount']) {
        throw Illuminate\Validation\ValidationException::withMessages([
            'amount' => 'Insufficient balance for this withdrawal request.',
        ]);
    }

    $withdrawal = DB::transaction(function () use ($user, $data) {
        $user->update([
            'bank_name' => $data['bank_name'],
            'bank_account_number' => $data['account_number'],
            'bank_account_holder' => $data['account_holder'],
            'withdrawal_account_type' => $data['account_type'] ?? ($user->withdrawal_account_type ?: 'bank'),
        ]);

        $withdrawalAmount = round((float) $data['amount'], 2);
        $processingFee = round($withdrawalAmount * 0.05, 2);
        $withdrawal = App\Models\Withdrawal::create([
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
        Mail::to($user->email)->send(new App\Mail\WithdrawalConfirmation($withdrawal->load('user')));
    } catch (\Throwable $e) {
        Log::warning('Failed to send withdrawal confirmation email', [
            'withdrawal_id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'error' => $e->getMessage(),
        ]);
    }

    return redirect()->route('withdraw')
        ->with('status', 'Withdrawal request submitted successfully.')
        ->with('receipt', [
            'reference' => $withdrawal->transaction_reference,
            'amount' => number_format((float) $withdrawal->amount, 2),
            'total_withdrawn' => number_format((float) $withdrawal->total_withdrawn, 2),
            'bank_name' => $withdrawal->bank_name,
            'account_number' => $withdrawal->account_number,
            'account_holder' => $withdrawal->account_holder,
            'status' => ucfirst($withdrawal->status),
            'submitted_at' => $withdrawal->created_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i'),
        ]);
})->middleware(['auth', RestrictUserAccess::class])->name('withdrawals.store');

Route::post('/withdrawal-account', function (Illuminate\Http\Request $request) {
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
        throw Illuminate\Validation\ValidationException::withMessages([
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

    $investments = Investment::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $withdrawals = Withdrawal::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $dailyInterest = $investments
        ->where('status', 'approved')
        ->sum(fn ($investment) => $investment->dailyInterestAmount());

    return view('history', [
        'investments' => $investments,
        'withdrawals' => $withdrawals,
        'dailyInterest' => $dailyInterest,
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

Route::get('/profile/edit', function () {
    return view('profile-edit');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.edit');

Route::get('/profile/password', function () {
    return view('change-password');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.password');

Route::get('/profile/notifications', function () {
    return view('notification-settings');
})->middleware(['auth', RestrictUserAccess::class])->name('profile.notifications');

Route::post('/investments', [InvestmentController::class, 'store'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('investments.store');

Route::get('/admin/dashboard', function () {
    abort_unless(Auth::user()?->is_admin, 403);

    return app(UserManagementController::class)->index(request());
})->middleware(['auth', RestrictUserAccess::class])->name('admin.dashboard');

Route::get('/admin/users/{user}', function (User $user) {
    abort_unless(Auth::user()?->is_admin, 403);

    return app(UserManagementController::class)->show($user);
})->middleware(['auth', RestrictUserAccess::class])->name('admin.users.show');

Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.destroy');

Route::post('/admin/users/{user}/restrict', [UserManagementController::class, 'restrict'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.users.restrict');

Route::post('/admin/backup', [UserManagementController::class, 'backup'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.backup');

Route::get('/admin/investments', [InvestmentApprovalController::class, 'index'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments');

Route::get('/admin/investments/{investment}', [InvestmentApprovalController::class, 'show'])
    ->middleware(['auth', RestrictUserAccess::class])
    ->name('admin.investments.show');

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
