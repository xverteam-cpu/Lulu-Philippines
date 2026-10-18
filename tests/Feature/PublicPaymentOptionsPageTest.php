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
            ->assertSee('Payment options are available without signing in.')
            ->assertSee('href="'.asset('PalawanPay.png').'" download="PalawanPay.png"', false)
            ->assertSee('href="'.asset('LandbankQR.png').'" download="LandbankQR.png"', false)
            ->assertSee('href="'.asset('BPIQR.png').'" download="BPIQR.png"', false);

        $this->assertFileExists(public_path('PalawanPay.png'));
        $this->assertFileExists(public_path('LandbankQR.png'));
        $this->assertFileExists(public_path('BPIQR.png'));
    }
}
