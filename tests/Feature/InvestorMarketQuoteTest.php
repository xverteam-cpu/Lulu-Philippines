<?php

namespace Tests\Feature;

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
            ->assertDontSee('4,587,021');
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
