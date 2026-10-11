<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardBondEarningsTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_dashboard_navigation_is_centered_in_a_full_width_safe_area_dock(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('@media (max-width: 520px)', false)
            ->assertSee('position: fixed;', false)
            ->assertSee('right: 0;', false)
            ->assertSee('left: 0;', false)
            ->assertSee('padding: 8px 14px calc(8px + env(safe-area-inset-bottom));', false)
            ->assertSee('width: min(430px, 100%);', false)
            ->assertSee('padding-bottom: calc(100px + env(safe-area-inset-bottom));', false);
    }

    public function test_bond_earnings_are_grouped_by_package_and_reflected_once_in_available_balance(): void
    {
        $user = User::factory()->create([
            'balance' => 50,
        ]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 1000,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDays(2),
            'status' => 'approved',
            'interest_days_credited' => 1,
            'last_interest_accrued_at' => now()->subDay(),
        ]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'loaded',
            'package_name' => 'Gold',
            'package_price' => 799,
            'amount' => 200,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 0.5,
            'duration_days' => 30,
            'starts_at' => now(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get('/dashboard');

        $response->assertOk()
            ->assertSee('Available Bonds')
            ->assertSee('EARNINGS')
            ->assertSee('Silver')
            ->assertSee('Gold')
            ->assertSee('Platinum')
            ->assertSee('$60.00')
            ->assertSee('$20.00')
            ->assertDontSee('$80.00');
    }

    public function test_dashboard_uses_live_account_values_and_dashboard_mockup_layout(): void
    {
        $user = User::factory()->create([
            'balance' => 82.5,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<html lang="en" class="dashboard-page">', false)
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"', false)
            ->assertSee('class="wallet-shell dashboard-shell"', false)
            ->assertSee('body.dashboard-page {', false)
            ->assertSee('overflow-x: clip;', false)
            ->assertSee('touch-action: pan-y;', false)
            ->assertSee('overscroll-behavior-x: none;', false)
            ->assertSee('html.dashboard-page {', false)
            ->assertSee('background-color: var(--dashboard-bg);', false)
            ->assertSee('box-sizing: border-box;', false)
            ->assertSee("document.addEventListener('gesturestart'", false)
            ->assertSee("if (event.touches.length > 1) event.preventDefault();", false)
            ->assertSee('html.dashboard-page::-webkit-scrollbar', false)
            ->assertSee('scrollbar-width: none;', false)
            ->assertSee('display: none;', false)
            ->assertSee('id="supportChatLauncher"', false)
            ->assertSee('id="supportChatPanel"', false)
            ->assertSee('class="dashboard-profile"', false)
            ->assertSee('class="dashboard-logo"', false)
            ->assertSee('LuLu Help')
            ->assertSee('Browse bond packages')
            ->assertSee('Open transactions')
            ->assertSee(route('invest.purchase', ['package' => 'crunch']), false)
            ->assertSee(route('history'), false)
            ->assertSee('id="dashboardBalanceToggle"', false)
            ->assertDontSee('9:41')
            ->assertDontSee('dashboard-statusbar')
            ->assertDontSee('dashboard-status-icons')
            ->assertSee('Purchase Bonds')
            ->assertSee('Apply for Franchise')
            ->assertSee(asset('logo.png'), false)
            ->assertSee(asset('banner-bonds.jpg'), false)
            ->assertSee(asset('banner-franchise.jpg'), false)
            ->assertSee(route('invest'), false)
            ->assertSee(route('franchising'), false);

        $html = $response->getContent();
        $profilePosition = strpos($html, 'class="dashboard-profile"');
        $logoPosition = strpos($html, 'class="dashboard-logo"');
        $this->assertNotFalse($profilePosition);
        $this->assertNotFalse($logoPosition);
        $this->assertLessThan($logoPosition, $profilePosition);

        $plainText = html_entity_decode(strip_tags($response->getContent()));
        $this->assertStringContainsString('$82.50', $plainText);
        $this->assertStringContainsString('$0.00', $plainText);
        $this->assertStringNotContainsString('$906.31', $plainText);
    }

    public function test_invest_page_sample_agreement_uses_the_preserved_six_page_contract_viewer(): void
    {
        $user = User::factory()->create();
        $viewerPath = public_path('lulu-bond-contract-preserved-pages.html');

        $this->actingAs($user)
            ->get(route('invest'))
            ->assertOk()
            ->assertSee('See sample bond agreement')
            ->assertSee('src="'.asset('lulu-bond-contract-preserved-pages.html').'"', false)
            ->assertSee("window.matchMedia('(max-width: 640px)').matches", false)
            ->assertSee('window.location.assign(sampleFrame.src)', false)
            ->assertDontSee('src="'.route('invest.agreement.sample').'"', false);

        $this->assertFileExists($viewerPath);
        $viewer = file_get_contents($viewerPath);
        $this->assertIsString($viewer);
        $this->assertStringContainsString('Agreement + Certificate · 6 preserved pages', $viewer);
        $this->assertStringContainsString('aria-label="Back to bonds"', $viewer);
        $this->assertStringContainsString('Bond Purchase Agreement, page 1', $viewer);
        $this->assertStringContainsString('Bond Certificate, page 6', $viewer);
        $this->assertStringContainsString('id="printContract"', $viewer);
        $this->assertStringContainsString("window.location.assign('/invest/agreement/sample/download')", $viewer);
        $this->assertStringContainsString('window.print();', $viewer);
    }

    public function test_mobile_sample_agreement_print_fallback_downloads_the_document(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('invest.agreement.sample.download'))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=Lulu-Bond-Agreement-and-Certificate-Draft.docx');
    }

    public function test_invest_page_shows_account_package_cards_under_total_investment(): void
    {
        Carbon::setTestNow('2026-10-01 15:00:00');
        Http::fake([
            'api.frankfurter.dev/*' => Http::response(['rates' => ['PHP' => 62.5]], 200),
        ]);

        $user = User::factory()->create();
        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 129,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 0.7,
            'duration_days' => 30,
            'starts_at' => now()->subDays(2),
            'status' => 'approved',
            'interest_days_credited' => 2,
            'last_interest_accrued_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('invest'));

        $response->assertOk()
            ->assertSee('invest-layout-redesign')
            ->assertSee('Your Bonds')
            ->assertSee('ACCOUNT PACKAGE')
            ->assertSee('Inactive')
            ->assertSee('Total earnings')
            ->assertSee('$1.80')
            ->assertSee('aria-label="Add to Silver package"', false)
            ->assertSee('aria-label="Activate Gold package"', false);

        $html = $response->getContent();
        $this->assertStringContainsString(
            '<section id="packageTrack" class="package-track" aria-label="Swipeable package list" hidden>',
            $html
        );
        $this->assertLessThan(
            strpos($html, 'Your Bonds'),
            strpos($html, '<section class="investment-balance-card"')
        );
        $this->assertLessThan(
            strpos($html, 'id="packageTrack"'),
            strpos($html, 'id="activePackageDeck"')
        );
        $this->assertStringContainsString('data-active-package-count="1"', $html);
        $this->assertStringContainsString('class="account-package-grid active-package-grid"', $html);
        $this->assertStringContainsString('aria-label="Activate Gold package"', $html);
    }

    public function test_only_approved_package_cards_are_in_the_swipeable_deck(): void
    {
        $user = User::factory()->create();

        foreach (['crunch', 'loaded'] as $packageKey) {
            Investment::create([
                'user_id' => $user->id,
                'package_key' => $packageKey,
                'package_name' => $packageKey === 'crunch' ? 'Silver' : 'Gold',
                'package_price' => $packageKey === 'crunch' ? 129 : 799,
                'amount' => $packageKey === 'crunch' ? 129 : 799,
                'payment_method' => 'account_balance',
                'daily_interest_rate' => 0.7,
                'duration_days' => 30,
                'starts_at' => now()->subDay(),
                'status' => 'approved',
            ]);
        }

        $response = $this->actingAs($user)->get(route('invest'));

        $response->assertOk()
            ->assertSee('data-active-package-count="2"', false)
            ->assertSee('aria-label="Silver account package, active', false)
            ->assertSee('aria-label="Gold account package, active', false)
            ->assertSee('aria-label="Platinum account package, inactive"', false)
            ->assertSee('aria-label="Activate Platinum package"', false)
            ->assertDontSee('aria-label="Gold account package, inactive"', false);

        $html = $response->getContent();
        $deckStart = strpos($html, 'id="activePackageDeck"');
        $deckEnd = strpos($html, 'id="activePackagePrevious"', $deckStart);
        $this->assertNotFalse($deckStart);
        $this->assertNotFalse($deckEnd);
        $deck = substr($html, $deckStart, $deckEnd - $deckStart);
        $this->assertSame(2, substr_count($deck, 'data-active-package-card'));
        $this->assertStringContainsString('aspect-ratio:1.62/1;', $html);
        $this->assertStringContainsString('margin:14px 0 20px;', $html);
        $this->assertStringContainsString('var limit = 56;', $html);
        $this->assertStringContainsString('(activeCurrent + step + activeCards.length) % activeCards.length', $html);
        $this->assertStringNotContainsString('outgoing.offsetHeight + 55', $html);
        $this->assertSame(1, substr_count($html, 'class="account-package-card account-package-card--empty'));
    }

    public function test_dashboard_purchase_bonds_action_opens_investment_packages_directly(): void
    {
        $user = User::factory()->create();

        $this->get(route('invest.advertisement'))
            ->assertRedirect(route('login'));

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('invest').'"', false)
            ->assertSee('View Bond Packages');

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('invest.advertisement'))
            ->assertRedirect(route('invest'));
    }

    public function test_approved_investments_without_starts_at_use_approved_at_for_accrual(): void
    {
        $user = User::factory()->create([
            'balance' => 0,
        ]);

        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 100,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => null,
            'approved_at' => now()->subDays(2),
            'status' => 'approved',
        ]);

        $accrued = \App\Support\DailyInterestAccrualService::accrueDueInterestForUser($user);

        $this->assertGreaterThan(0, $accrued);
        $this->assertGreaterThan(0, (float) $investment->fresh()->interest_days_credited);
        $this->assertGreaterThan(0, (float) $user->fresh()->balance);
    }

    public function test_interest_starts_the_day_after_investment_and_history_shows_each_credited_day(): void
    {
        Carbon::setTestNow('2026-09-23 15:00:00');
        $user = User::factory()->create(['balance' => 0]);
        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 129,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 0.7,
            'duration_days' => 30,
            'starts_at' => now(),
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('history'))
            ->assertOk()
            ->assertViewHas('dailyInterestEntries', fn ($entries) => $entries->isEmpty())
            ->assertViewHas('dailyInterest', 0.0);

        $this->assertSame(0, (int) $investment->fresh()->interest_days_credited);
        $this->assertSame(0.0, (float) $user->fresh()->balance);

        Carbon::setTestNow('2026-10-01 15:00:00');

        $this->get(route('history'))
            ->assertOk()
            ->assertViewHas('dailyInterest', 7.2)
            ->assertViewHas('dailyInterestEntries', function ($entries) {
                return $entries->count() === 8
                    && $entries->first()['date']->toDateString() === '2026-10-01'
                    && $entries->last()['date']->toDateString() === '2026-09-24'
                    && $entries->every(fn (array $entry) => $entry['amount'] === 0.9);
            })
            ->assertSee('Interest Earned To Date')
            ->assertSee('$7.20');

        $this->assertSame(8, (int) $investment->fresh()->interest_days_credited);
        $this->assertSame(7.2, (float) $user->fresh()->balance);
    }
}
