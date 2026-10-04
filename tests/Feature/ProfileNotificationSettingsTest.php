<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileNotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_settings_render_saved_preferences_and_update_them_for_the_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.notifications'))
            ->assertOk()
            ->assertSee('<html lang="en" class="profile-notifications-page">', false)
            ->assertSee('Notification Settings | Lulu')
            ->assertSee('Stay in the loop')
            ->assertSee('Email notifications')
            ->assertSee('Account activity alerts')
            ->assertSee('Marketing updates')
            ->assertSee(route('profile.notifications.update'), false)
            ->assertSee('id="emailNotifications" name="preferences[email_notifications]" type="checkbox" value="1" checked', false)
            ->assertSee('id="accountAlerts" name="preferences[account_alerts]" type="checkbox" value="1" checked', false)
            ->assertDontSee('id="marketingUpdates" name="preferences[marketing_updates]" type="checkbox" value="1" checked', false);

        $this->post(route('profile.notifications.update'), [
            'preferences' => [
                'email_notifications' => '0',
                'account_alerts' => '1',
                'marketing_updates' => '1',
            ],
        ])
            ->assertRedirect(route('profile.notifications'))
            ->assertSessionHas('status', 'Notification preferences saved.');

        $this->assertSame([
            'email_notifications' => false,
            'account_alerts' => true,
            'marketing_updates' => true,
        ], $user->fresh()->notification_preferences);

        $this->actingAs($user)
            ->get(route('profile.notifications'))
            ->assertOk()
            ->assertDontSee('id="emailNotifications" name="preferences[email_notifications]" type="checkbox" value="1" checked', false)
            ->assertSee('id="accountAlerts" name="preferences[account_alerts]" type="checkbox" value="1" checked', false)
            ->assertSee('id="marketingUpdates" name="preferences[marketing_updates]" type="checkbox" value="1" checked', false);
    }

    public function test_notification_preferences_require_all_supported_boolean_options(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.notifications'))
            ->post(route('profile.notifications.update'), [
                'preferences' => [
                    'email_notifications' => 'not-a-boolean',
                    'account_alerts' => '1',
                ],
            ])
            ->assertRedirect(route('profile.notifications'))
            ->assertSessionHasErrors([
                'preferences.email_notifications',
                'preferences.marketing_updates',
            ]);

        $this->assertNull($user->fresh()->notification_preferences);
    }
}
