<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\BlockBlockedIp;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleOAuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_redirects_to_oauth_provider(): void
    {
        $this->withoutMiddleware(BlockBlockedIp::class);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect()->away('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get('/login/google')
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_and_authenticates_verified_user(): void
    {
        $this->withoutMiddleware(BlockBlockedIp::class);

        $googleUser = SocialiteUser::fake([
            'id' => 'google-user-123',
            'name' => 'Google User',
            'email' => 'google.user@example.com',
            'verified_email' => true,
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $user = User::where('email', 'google.user@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->is_admin);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_rejects_unverified_email(): void
    {
        $this->withoutMiddleware(BlockBlockedIp::class);

        $googleUser = SocialiteUser::fake([
            'id' => 'google-user-456',
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'verified_email' => false,
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get('/auth/google/callback')
            ->assertRedirect(route('investors'))
            ->assertSessionHasErrors('google');

        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }
}
