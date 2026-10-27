<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthSignupTest extends TestCase
{
    use RefreshDatabase;
    public function test_signup_page_displays_a_registration_form(): void
    {
        $response = $this->get('/signup');

        $response->assertOk();
        $response->assertSee('Sign Up');
        $response->assertSee('Fullname');
        $response->assertSee('Username');
        $response->assertSee('Referral');
        $response->assertSee('Create password');
        $response->assertSee('Email Address');
        $response->assertSee('name="email"', false);
        $response->assertSee('Mobile Number');
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="referral"', false)
            ->assertSee('name="referral" type="text"', false);
    }

    public function test_signup_page_preserves_the_referrer_in_the_google_signup_link(): void
    {
        $referrer = User::factory()->create(['username' => 'referrer']);

        $this->get(route('signup', ['ref' => $referrer->referral_code]))
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee(route('login.google', ['ref' => $referrer->referral_code]), false);
    }

    public function test_authenticated_user_sees_a_continue_link_to_their_dashboard_on_investor_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('investors'))
            ->assertOk()
            ->assertSee('class="investors-dashboard-continue" href="'.route('dashboard').'"', false)
            ->assertSee('.investors-login-form > :not(.investors-dashboard-continue)', false);
    }

    public function test_authenticated_admin_continue_link_opens_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('investors'))
            ->assertOk()
            ->assertSee('class="investors-dashboard-continue" href="'.route('admin.dashboard').'"', false);
    }

    public function test_user_session_expires_after_thirty_minutes_of_inactivity(): void
    {
        $this->assertSame(30, config('session.lifetime'));
        $this->assertFalse((bool) config('session.expire_on_close'));
    }

    public function test_manual_signup_creates_and_authenticates_a_user_with_a_normalized_email(): void
    {
        Mail::fake();
        $referrer = User::factory()->create(['name' => 'Wilmer Pasco']);
        $response = $this->post('/register-partner', [
            'fullname' => 'Jane Doe',
            'username' => 'jane',
            'email' => '  Jane.Doe@Example.com ',
            'phone' => '+639171234567',
            'referral' => $referrer->referral_code,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $user = User::where('username', 'jane')->firstOrFail();
        $this->assertDatabaseHas('users', [
            'username' => 'jane',
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '+639171234567',
        ]);
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->assertTrue(User::where('username', 'jane')->exists());
        $this->assertSame($referrer->id, $user->referred_by);
        $this->assertNull($user->phone_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_manual_signup_requires_a_valid_referral_code(): void
    {
        Mail::fake();

        $this->from(route('signup'))
            ->post('/register-partner', [
                'fullname' => 'Jane Doe',
                'username' => 'jane',
                'email' => 'jane@example.com',
                'phone' => '09171234567',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('signup'))
            ->assertSessionHasErrors('referral');

        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_manual_signup_rejects_an_unknown_referral_code(): void
    {
        Mail::fake();

        $this->from(route('signup'))
            ->post('/register-partner', [
                'fullname' => 'Jane Doe',
                'username' => 'jane',
                'email' => 'jane@example.com',
                'phone' => '09171234567',
                'referral' => 'not-a-real-referral',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('signup'))
            ->assertSessionHasErrors('referral');

        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_referral_codes_use_first_name_and_surname_initial_and_are_unique(): void
    {
        $firstUser = User::factory()->create(['name' => 'Wilmer Pasco']);
        $secondUser = User::factory()->create(['name' => 'Wilmer Pasco']);

        $this->assertSame('WilmerP', $firstUser->referral_code);
        $this->assertSame('WilmerP2', $secondUser->referral_code);
    }

    public function test_admin_user_detail_includes_a_copyable_referral_link(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => 'Wilmer Pasco']);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('Copy referral link')
            ->assertSee(route('signup', ['ref' => 'WilmerP']), false);
    }

    public function test_password_login_remembers_the_user_across_browser_sessions(): void
    {
        $user = User::factory()->create([
            'username' => 'jane-login',
            'email' => 'jane-login@example.com',
            'password' => 'password123',
            'remember_token' => null,
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->username,
            'password' => 'password123',
        ])->assertRedirect(route('phone.capture'));

        $this->assertNotEmpty($user->fresh()->remember_token);
    }

    public function test_email_login_ignores_case_and_surrounding_whitespace(): void
    {
        $user = User::factory()->create([
            'email' => 'Jane.Login@Example.com',
            'password' => 'password123',
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->post(route('login.submit'), [
            'email' => '  jane.login@example.com  ',
            'password' => 'password123',
        ])->assertRedirect(route('phone.capture'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_generates_a_unique_username_when_none_is_provided(): void
    {
        Mail::fake();

        $referrer = User::factory()->create(['username' => 'janedoe']);
        $this->post(route('register.partner'), [
            'fullname' => 'Jane Doe',
            'username' => '',
            'email' => 'jane.doe@example.com',
            'phone' => '+639171234567',
            'referral' => $referrer->referral_code,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'username' => 'janedoe-1',
            'email' => 'jane.doe@example.com',
        ]);
    }

    public function test_registration_rejects_an_email_that_is_already_registered_with_different_case(): void
    {
        Mail::fake();

        User::factory()->create(['email' => 'Jane@example.com']);
        $referrer = User::factory()->create(['name' => 'Wilmer Pasco']);

        $this->from(route('signup'))
            ->post(route('register.partner'), [
                'fullname' => 'Jane Doe',
                'username' => 'jane-doe',
                'email' => 'jane@example.com',
                'phone' => '+639171234567',
                'referral' => $referrer->referral_code,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('signup'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 2);
    }

    public function test_signup_requires_a_mobile_number(): void
    {
        Mail::fake();
        $referrer = User::factory()->create();

        $this->from(route('signup'))
            ->post(route('register.partner'), [
                'fullname' => 'Jane Doe',
                'username' => 'jane',
                'email' => 'jane@example.com',
                'referral' => $referrer->referral_code,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('signup'))
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_existing_user_must_add_a_mobile_number_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => 'password123',
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('phone.capture'));

        $this->get(route('phone.capture'))
            ->assertOk()
            ->assertSee('Add your mobile number')
            ->assertSee('name="phone"', false);

        $this->post(route('phone.store'), [
            'phone' => '09171234567',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '09171234567',
        ]);
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_admin_user_detail_displays_the_saved_mobile_number(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['phone' => '+639171234567']);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('Mobile Number')
            ->assertSee('+639171234567')
            ->assertSee('Phone Verification')
            ->assertSee('SMS verification temporarily disabled');
    }

    public function test_existing_phone_can_be_saved_without_sms_verification(): void
    {
        $user = User::factory()->create([
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->post(route('phone.store'), [
                'phone' => '09171234567',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertSame('09171234567', $user->fresh()->phone);
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_authenticated_users_cannot_bypass_required_phone_entry_by_opening_another_page(): void
    {
        $user = User::factory()->create([
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertRedirect(route('phone.capture'));

        $this->actingAs($user)
            ->getJson(route('profile.edit'))
            ->assertForbidden()
            ->assertJsonPath('redirect_url', route('phone.capture'));
    }
}
