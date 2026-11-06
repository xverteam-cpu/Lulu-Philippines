<?php

namespace Tests\Feature;

use App\Models\AccountActivity;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyPreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_persist_php_as_their_account_currency_and_see_converted_balance(): void
    {
        $this->fakeExchangeRate();
        $user = User::factory()->create(['balance' => 100]);

        $this->actingAs($user)
            ->post(route('currency.preference'), ['preferred_currency' => 'PHP'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame('PHP', $user->fresh()->preferred_currency);

        $this->actingAs($user)
            ->get(route('send'))
            ->assertOk()
            ->assertSee('₱6,000.00');
    }

    public function test_dashboard_shows_php_balances_and_switches_back_to_usd(): void
    {
        $this->fakeExchangeRate();
        $user = User::factory()->create([
            'balance' => 100,
            'preferred_currency' => 'PHP',
        ]);

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('₱')
            ->assertSee('6,000')
            ->assertSee('name="preferred_currency" value="USD"', false);
    }

    public function test_php_user_transfer_is_converted_before_usd_account_balances_are_updated(): void
    {
        $this->fakeExchangeRate();
        $sender = User::factory()->create(['balance' => 100, 'preferred_currency' => 'PHP']);
        $recipient = User::factory()->create(['balance' => 5]);

        $response = $this->actingAs($sender)
            ->post(route('send.transfer'), [
                'recipient' => $recipient->email,
                'amount' => '600.00',
            ]);

        $sentActivity = AccountActivity::query()
            ->where('user_id', $sender->id)
            ->where('type', 'funds_sent')
            ->firstOrFail();
        $response->assertRedirect(route('transfers.receipt', $sentActivity))
            ->assertSessionHas('status', 'Sent ₱600.00 to '.$recipient->name.'.');

        $this->assertSame(90.0, (float) $sender->fresh()->balance);
        $this->assertSame(15.0, (float) $recipient->fresh()->balance);
        $this->assertDatabaseHas('account_activities', [
            'user_id' => $sender->id,
            'amount' => 10,
        ]);
    }

    public function test_php_user_withdrawal_amount_is_converted_before_usd_limits_and_storage(): void
    {
        $this->fakeExchangeRate();
        $user = User::factory()->create([
            'balance' => 100,
            'preferred_currency' => 'PHP',
        ]);

        $this->actingAs($user)
            ->post(route('withdrawals.store'), [
                'amount' => '1500.00',
                'bank_name' => 'BPI',
                'account_number' => '1234567890',
                'account_holder' => $user->name,
                'account_type' => 'bank',
            ])
            ->assertRedirect(route('withdraw'))
            ->assertSessionHas('receipt.display_amount', '₱1,500.00');

        $this->assertSame(75.0, (float) $user->fresh()->balance);
        $this->assertSame(25.0, (float) Withdrawal::query()->sole()->amount);
    }

    public function test_investment_purchase_defaults_to_the_saved_currency_preference(): void
    {
        $this->fakeExchangeRate();
        $user = User::factory()->create(['preferred_currency' => 'PHP']);

        $this->actingAs($user)
            ->get(route('invest.purchase', ['package' => 'crunch']))
            ->assertOk()
            ->assertSee('id="purchaseCurrency" value="PHP"', false)
            ->assertSee('value="7740.00"', false);

        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'https://api.frankfurter.dev/v1/latest')
            && $request->data()['symbols'] === 'PHP');
    }

    private function fakeExchangeRate(): void
    {
        Cache::forget('usd_to_php_rate_meta_v2');
        Cache::forget('usd_to_php_rate_view_meta_v1');
        Http::fake([
            'api.frankfurter.dev/v1/latest*' => Http::response([
                'amount' => 1,
                'base' => 'USD',
                'date' => '2026-10-01',
                'rates' => ['PHP' => 60.0],
            ]),
        ]);
    }
}
