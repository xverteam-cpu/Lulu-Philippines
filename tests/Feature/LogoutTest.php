<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_invalidates_the_authenticated_session_and_redirects_to_investors(): void
    {
        $user = User::factory()->create();
        $secondUser = User::factory()->create([
            'username' => 'second-login',
            'password' => 'password123',
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('investors'))
            ->assertHeader('Cache-Control', 'must-revalidate, no-cache, no-store, private')
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0');

        $this->assertGuest();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('investors'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'must-revalidate, no-cache, no-store, private')
            ->assertSee('name="_token"', false);

        $this->post(route('login.submit'), [
            'email' => $secondUser->username,
            'password' => 'password123',
        ])->assertRedirect(route('phone.capture'));

        $this->assertAuthenticatedAs($secondUser);
    }

    public function test_authenticated_pages_are_not_cached_and_reload_when_restored_from_browser_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'must-revalidate, no-cache, no-store, private')
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0')
            ->assertSee("if (!event.persisted) return;", false)
            ->assertSee("document.documentElement.style.visibility = 'hidden';", false)
            ->assertSee('window.location.reload();', false);
    }
}
