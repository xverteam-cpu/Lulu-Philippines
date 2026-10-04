<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\BlockBlockedIp;
use App\Mail\WelcomeEmail;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Illuminate\Support\Facades\Mail;
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
        Mail::fake();

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
        $this->assertNotEmpty($user->remember_token);
        $response->assertRedirect(route('dashboard'));
        Mail::assertSent(WelcomeEmail::class, fn (WelcomeEmail $mail): bool => $mail->hasTo($user->email));
    }

    public function test_google_signup_preserves_referral_attribution_for_the_new_account(): void
    {
        $this->withoutMiddleware(BlockBlockedIp::class);

        $referrer = User::factory()->create([
            'name' => 'Referral Sponsor',
            'username' => 'sponsor',
        ]);

        $this->get(route('signup', ['ref' => $referrer->username]))
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee(route('login.google', ['ref' => $referrer->username]), false);

        $googleUser = SocialiteUser::fake([
            'id' => 'google-referred-user',
            'name' => 'Referred Google User',
            'email' => 'referred.google@example.com',
            'verified_email' => true,
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect()->away('https://accounts.google.com/o/oauth2/auth'));
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')
            ->twice()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('login.google', ['ref' => $referrer->username]))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');

        $this->get(route('login.google.callback'))
            ->assertRedirect(route('dashboard'));

        $referredUser = User::where('email', 'referred.google@example.com')->firstOrFail();

        $this->assertSame($referrer->id, $referredUser->referred_by);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->get(route('admin.users.show', $referredUser))
            ->assertOk()
            ->assertSee('Referred By')
            ->assertSee('Referral Sponsor')
            ->assertSee('@sponsor');
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
