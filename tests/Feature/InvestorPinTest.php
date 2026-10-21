<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class InvestorPinTest extends TestCase
{
    use RefreshDatabase;

    public function test_returning_investor_sees_branded_pin_card_without_replacing_the_page(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $response = $this->actingAs($user)->get(route('investors'));

        $response->assertOk()
            ->assertSee('Investor Relations')
            ->assertSee('Enter your 4-digit PIN to continue.')
            ->assertSeeInOrder([
                '<form class="investors-pin-lock-form" method="post" action="'.route('investor.pin.verify').'" data-pin-lock="true">',
                'name="_token"',
                'class="pin-signout investors-pin-signout" type="submit" formaction="'.route('logout').'"',
            ], false)
            ->assertSee("document.querySelector('.investors-pin-lock-form[data-pin-lock=\"true\"]')", false)
            ->assertSee('Forgot your PIN? Reset your password to set a new one.')
            ->assertSee('Secure investor access')
            ->assertSee('grid-template-columns: repeat(3, minmax(0, 1fr));', false)
            ->assertSee('justify-content: center;', false)
            ->assertSee('min-width: 0;', false)
            ->assertSee('background: #fff;', false)
            ->assertSee('investor-mobile-background.png')
            ->assertSee('cxcx.png')
            ->assertDontSee('investors-dashboard-continue');

        $this->assertSame(4, substr_count($response->getContent(), '<span class="pin-dot"></span>'));
    }

    public function test_investor_can_sign_out_from_the_pin_screen(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user)
            ->get(route('investors'))
            ->assertOk()
            ->assertSee('data-pin-lock="true"', false)
            ->assertSee('formaction="'.route('logout').'"', false);

        $this->post(route('logout'))
            ->assertRedirect(route('investors'));
        $this->assertGuest();

        $this->get(route('investors'))
            ->assertOk()
            ->assertSee('Your everyday.')
            ->assertSee('Continue with Google')
            ->assertDontSee('Welcome back.');
    }

    public function test_guest_sees_the_investor_relations_login_without_the_old_page_layout(): void
    {
        $this->get(route('investors'))
            ->assertOk()
            ->assertSee('Investor Relations')
            ->assertSee('Your everyday.')
            ->assertSee('Continue with Google')
            ->assertSee('Create an account')
            ->assertDontSee('Lulu Retail Holdings')
            ->assertDontSee('Where The World Comes To Shop')
            ->assertDontSee('Create your PIN');
    }

    public function test_dashboard_redirects_to_investors_until_pin_is_verified(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('investors'));
    }

    public function test_correct_pin_unlocks_dashboard_for_the_authenticated_user(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user)
            ->post(route('investor.pin.verify'), ['pin' => '1234'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('investor_pin_verified', true);

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_user_can_sign_in_on_a_fresh_browser_session_then_verify_their_pin(): void
    {
        $user = User::factory()->create([
            'password' => 'Password123',
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'Password123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('investors'));

        $this->get(route('investors'))
            ->assertOk()
            ->assertSee('Enter your 4-digit PIN to continue.')
            ->assertDontSee("step==='identity'");

        $this->post(route('investor.pin.verify'), ['pin' => '1234'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('investor_pin_verified', true);

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_incorrect_pin_does_not_unlock_dashboard(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user)
            ->from(route('investors'))
            ->post(route('investor.pin.verify'), ['pin' => '9876'])
            ->assertRedirect(route('investors'))
            ->assertSessionHasErrors('pin')
            ->assertSessionMissing('investor_pin_verified');
    }

    public function test_six_digit_legacy_pin_is_rejected_and_password_reset_clears_it(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
        ]);

        $this->actingAs($user)
            ->from(route('investors'))
            ->post(route('investor.pin.verify'), ['pin' => '123456'])
            ->assertRedirect(route('investors'))
            ->assertSessionHasErrors('pin')
            ->assertSessionMissing('investor_pin_verified');

        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('order'));

        $this->assertNull($user->fresh()->pin_hash);
        $this->assertNull($user->fresh()->pin_set_at);

        $this->actingAs($user->fresh())
            ->get(route('investors'))
            ->assertOk()
            ->assertSee('Create your PIN');
    }

    public function test_user_without_a_pin_can_set_one_and_then_access_the_dashboard(): void
    {
        $user = User::factory()->create(['pin_hash' => null]);

        $response = $this->actingAs($user)
            ->get(route('investors'))
            ->assertOk()
            ->assertSee('Create your PIN');

        $this->assertSame(4, substr_count($response->getContent(), '<span class="pin-dot"></span>'));

        $this->actingAs($user)
            ->post(route('investor.pin.setup'), [
                'pin' => '2468',
                'pin_confirmation' => '2468',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('investor_pin_verified', true);

        $this->assertTrue(Hash::check('2468', $user->fresh()->pin_hash));
        $this->assertNotNull($user->fresh()->pin_set_at);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_correct_pin_returns_admin_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'pin_hash' => Hash::make('1357'),
        ]);

        $this->actingAs($admin)
            ->post(route('investor.pin.verify'), ['pin' => '1357'])
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_existing_pin_cannot_be_replaced_through_setup(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user)
            ->from(route('investors'))
            ->post(route('investor.pin.setup'), [
                'pin' => '2468',
                'pin_confirmation' => '2468',
            ])
            ->assertRedirect(route('investors'))
            ->assertSessionHasErrors('pin');

        $this->assertTrue(Hash::check('1234', $user->fresh()->pin_hash));
    }

    public function test_pin_verification_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('investor.pin.verify'), ['pin' => '9999']);
        }

        $this->post(route('investor.pin.verify'), ['pin' => '9999'])
            ->assertTooManyRequests();
    }
}
