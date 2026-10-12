<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InvestorMarketQuoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('investors.lulu_retail_quote.v1');
        Cache::forget('illuminate:cache:flexible:created:investors.lulu_retail_quote.v1');
    }

    protected function tearDown(): void
    {
        Cache::forget('investors.lulu_retail_quote.v1');
        Cache::forget('illuminate:cache:flexible:created:investors.lulu_retail_quote.v1');
        parent::tearDown();
    }

    public function test_investor_page_keeps_ticker_design_and_renders_live_quote_fields(): void
    {
        $this->get(route('investors'))
            ->assertOk()
            ->assertSee('Lulu Retail Holdings')
            ->assertSee('Last Price')
            ->assertSee('Change')
            ->assertSee('Volume')
            ->assertSee('data-market-quote="price"', false)
            ->assertSee('data-market-quote="change"', false)
            ->assertSee('data-market-quote="volume"', false)
            ->assertSee('data-market-quote="timestamp"', false)
            ->assertSee('0.95')
            ->assertSee('4,587,021')
            ->assertSee('04/09/2026 14:55');
    }

    public function test_invest_page_shows_the_live_quote_ticker_at_the_bottom_of_the_balance_card(): void
    {
        Http::fake([
            'api.frankfurter.dev/v1/latest*' => Http::response([
                'date' => '2026-10-12',
                'rates' => ['PHP' => 61.31],
            ]),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('invest'))
            ->assertOk()
            ->assertSee('Lulu Retail')
            ->assertSee('Last Price')
            ->assertSee('Change')
            ->assertSee('Volume')
            ->assertSee('0.95')
            ->assertSee('4,587,021')
            ->assertSee('04/09/2026 14:55')
            ->assertSee('data-market-quote-url="'.route('investors.market-quote').'"', false)
            ->assertSee('src="'.asset('market-quote.js').'" defer', false)
            ->assertSee('invest-market-ticker-scroll', false);

        $html = $response->getContent();
        $cardStart = strpos($html, '<section class="investment-balance-card"');
        $tickerPosition = strpos($html, 'class="invest-market-ticker"');
        $cardEnd = strpos($html, '</section>', $cardStart);

        $this->assertNotFalse($cardStart);
        $this->assertNotFalse($tickerPosition);
        $this->assertNotFalse($cardEnd);
        $this->assertGreaterThan($cardStart, $tickerPosition);
        $this->assertLessThan($cardEnd, $tickerPosition);
    }

    public function test_quote_endpoint_fetches_and_returns_valid_eodhd_quote(): void
    {
        config([
            'market_data.eodhd.token' => 'test-token',
            'market_data.eodhd.symbol' => 'LULU.ADX',
        ]);
        Http::fake([
            'eodhd.com/api/real-time/LULU.ADX*' => Http::response([
                'close' => 0.96,
                'change' => 0.01,
                'change_p' => 1.05,
                'volume' => 123456,
                'timestamp' => 1791839700,
            ]),
        ]);

        $this->getJson(route('investors.market-quote'))
            ->assertOk()
            ->assertExactJson([
                'price' => 0.96,
                'change' => 0.01,
                'change_percent' => 1.05,
                'volume' => 123456,
                'timestamp' => 1791839700,
            ]);

        Http::assertSent(fn (Request $request): bool => str_starts_with(
            $request->url(),
            'https://eodhd.com/api/real-time/LULU.ADX'
        )
            && $request->data()['api_token'] === 'test-token'
            && $request->data()['fmt'] === 'json');
    }

    public function test_quote_endpoint_reports_unavailable_when_provider_token_is_missing(): void
    {
        config(['market_data.eodhd.token' => null]);
        Http::fake();

        $this->getJson(route('investors.market-quote'))
            ->assertServiceUnavailable()
            ->assertJson(['message' => 'Market quote is currently unavailable.']);

        Http::assertNothingSent();
    }
}
