<?php

use App\Support\DailyInterestAccrualService;
use App\Support\CurrencyRateService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('interest:accrue', function () {
    $total = DailyInterestAccrualService::accrueDueInterest();
    $this->info('Daily interest accrued: $' . number_format($total, 2));
})->describe('Credit due daily interest for approved investments');

Artisan::command('currency:refresh', function () {
    $rate = CurrencyRateService::latestUsdToPhp();
    $this->info('USD to PHP rate refreshed: ' . $rate);
})->describe('Refresh the USD to PHP conversion rate from Frankfurter');
