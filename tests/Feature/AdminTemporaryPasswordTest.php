<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTemporaryPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_single_use_temporary_password_for_a_user(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'pin_hash' => Hash::make('123456'),
        ]);
        $user = User::factory()->create(['password' => 'current-password']);

        $this->actingAs($admin)
            ->post(route('admin.users.temporary-password', $user))
            ->assertRedirect(route('admin.dashboard'));

        $temporaryPassword = session('temporary_password');
        $this->assertIsString($temporaryPassword);
        $this->assertNotSame('current-password', $temporaryPassword);
        $this->assertTrue(Hash::check($temporaryPassword, $user->fresh()->temporary_password_hash));

        $this->withSession(['investor_pin_verified' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($temporaryPassword)
            ->assertSee('Create Temporary Password')
            ->assertSee('Delete Account')
            ->assertSee(route('admin.users.destroy', $user), false);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee($temporaryPassword);
    }

    public function test_user_must_replace_the_single_use_password_before_signing_in(): void
    {
        $user = User::factory()->create([
            'email' => 'temporary@example.com',
            'phone' => '+639171234567',
            'password' => 'original-password',
            'temporary_password_hash' => Hash::make('one-time-password'),
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'one-time-password',
        ])->assertRedirect(route('temporary-password.change'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'temporary_password_hash' => null,
        ]);

        $this->get(route('temporary-password.change'))
            ->assertOk()
            ->assertSee('Choose a new password');

        $this->post(route('temporary-password.update'), [
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('replacement-password', $user->fresh()->password));
    }

    public function test_normal_password_login_still_works_for_users_without_a_temporary_password(): void
    {
        $user = User::factory()->create([
            'email' => 'regular@example.com',
            'phone' => '+639171234567',
            'password' => 'regular-password',
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'regular-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->temporary_password_hash);
    }

    public function test_non_admin_cannot_create_a_temporary_password(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.users.temporary-password', $target))
            ->assertForbidden();

        $this->assertNull($target->fresh()->temporary_password_hash);
    }

    public function test_admin_cannot_create_a_temporary_password_for_another_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $targetAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.users.temporary-password', $targetAdmin))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHasErrors('user');

        $this->assertNull($targetAdmin->fresh()->temporary_password_hash);
    }
}
