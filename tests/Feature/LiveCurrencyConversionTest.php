<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveCurrencyConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_php_investment_amount_is_converted_using_the_current_provider_rate(): void
    {
        Cache::forget('usd_to_php_rate_meta_v2');
        Http::fake([
            'api.frankfurter.dev/v1/latest*' => Http::response([
                'amount' => 1,
                'base' => 'USD',
                'date' => '2026-10-01',
                'rates' => ['PHP' => 60.0],
            ]),
        ]);

        $user = User::factory()->create([
            'pin_hash' => bcrypt('123456'),
        ]);

        $this->actingAs($user)
            ->withSession(['pin_verified' => true])
            ->post(route('investments.store'), [
                'package' => 'crunch',
                'amount' => 8000,
                'currency' => 'PHP',
                'payment_method' => 'bank_transfer',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'amount' => 133.33,
            'status' => 'pending',
        ]);

        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'https://api.frankfurter.dev/v1/latest')
            && $request->data()['base'] === 'USD'
            && $request->data()['symbols'] === 'PHP');
    }
}
