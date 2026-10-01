<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertDontSee('Email');
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

    public function test_signup_form_can_create_a_user_with_the_simplified_fields(): void
    {
        $response = $this->post('/register-partner', [
            'fullname' => 'Jane Doe',
            'username' => 'jane',
            'referral' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/pin/setup');
        $user = User::where('username', 'jane')->firstOrFail();
        $this->assertDatabaseHas('users', [
            'username' => 'jane',
            'name' => 'Jane Doe',
        ]);
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->assertTrue(User::where('username', 'jane')->exists());
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
}
