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
        $this->assertDatabaseHas('users', [
            'username' => 'jane',
            'name' => 'Jane Doe',
        ]);
        $this->assertTrue(User::where('username', 'jane')->exists());
    }
}
