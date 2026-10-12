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
    }

    public function test_signup_page_preserves_the_referrer_in_the_google_signup_link(): void
    {
        $referrer = User::factory()->create(['username' => 'referrer']);

        $this->get(route('signup', ['ref' => $referrer->username]))
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee(route('login.google', ['ref' => $referrer->username]), false);
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

        $response = $this->post('/register-partner', [
            'fullname' => 'Jane Doe',
            'username' => 'jane',
            'email' => '  Jane.Doe@Example.com ',
            'referral' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $user = User::where('username', 'jane')->firstOrFail();
        $this->assertDatabaseHas('users', [
            'username' => 'jane',
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->assertTrue(User::where('username', 'jane')->exists());
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_login_remembers_the_user_across_browser_sessions(): void
    {
        $user = User::factory()->create([
            'username' => 'jane-login',
            'email' => 'jane-login@example.com',
            'password' => 'password123',
            'remember_token' => null,
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->username,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertNotEmpty($user->fresh()->remember_token);
    }

    public function test_email_login_ignores_case_and_surrounding_whitespace(): void
    {
        $user = User::factory()->create([
            'email' => 'Jane.Login@Example.com',
            'password' => 'password123',
        ]);

        $this->post(route('login.submit'), [
            'email' => '  jane.login@example.com  ',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_generates_a_unique_username_when_none_is_provided(): void
    {
        Mail::fake();

        User::factory()->create(['username' => 'janedoe']);

        $this->post(route('register.partner'), [
            'fullname' => 'Jane Doe',
            'username' => '',
            'email' => 'jane.doe@example.com',
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

        $this->from(route('signup'))
            ->post(route('register.partner'), [
                'fullname' => 'Jane Doe',
                'username' => 'jane-doe',
                'email' => 'jane@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('signup'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }
}
