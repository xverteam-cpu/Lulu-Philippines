<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPaymentOptionsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_options_page_is_public_and_links_to_all_qr_downloads(): void
    {
        $response = $this->get(route('payment-options'));

        $response->assertOk()
            ->assertSee('<title>Payment Options | Lulu Philippines</title>', false)
            ->assertSee('does not require an account sign-in')
            ->assertSee('Scan to pay')
            ->assertSee('Choose a payment method')
            ->assertSee('aria-current="true"', false)
            ->assertSee('href="'.asset('BPIQR.png').'" download="BPIQR.png"', false)
            ->assertSee(route('payment-options', ['provider' => 'landbank']), false)
            ->assertSee(route('payment-options', ['provider' => 'palawanpay']), false);

        $providers = [
            'bpi' => ['BPI', 'BPIQR.png'],
            'landbank' => ['Landbank', 'LandbankQR.png'],
            'palawanpay' => ['Palawan Pay', 'PalawanPay.png'],
        ];

        foreach ($providers as $key => [$name, $qr]) {
            $this->get(route('payment-options', ['provider' => $key]))
                ->assertOk()
                ->assertSee("Download {$name} QR")
                ->assertSee('src="'.asset($qr).'"', false)
                ->assertSee('href="'.asset($qr).'" download="'.$qr.'"', false);
        }

        $this->assertFileExists(public_path('PalawanPay.png'));
        $this->assertFileExists(public_path('LandbankQR.png'));
        $this->assertFileExists(public_path('BPIQR.png'));
    }

    public function test_payment_options_returns_not_found_for_an_unknown_provider(): void
    {
        $this->get(route('payment-options', ['provider' => 'unknown']))
            ->assertNotFound();
    }
}
