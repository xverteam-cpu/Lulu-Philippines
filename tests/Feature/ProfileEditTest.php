<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_and_update_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'region' => 'Old Region',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(route('profile.update'), false);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'region' => 'New Region',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'region' => 'New Region',
        ]);
    }

    public function test_profile_update_rejects_an_email_already_used_by_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => $otherUser->email,
                'region' => '',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
