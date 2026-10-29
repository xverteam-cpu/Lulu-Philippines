<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetInvestorPinNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_card_links_to_working_password_recovery(): void
    {
        $this->get(route('investors'))
            ->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee(route('password.request'));

        $user = User::factory()->create([
            'email' => 'recover@example.com',
            'password' => 'OldPassword123',
        ]);
        Notification::fake();

        $this->get(route('password.request'))->assertOk();
        $this->post(route('password.email'), ['email' => ' RECOVER@example.com '])
            ->assertRedirect()
            ->assertSessionHas('status');

        $notification = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $sent) use (&$notification): bool {
            $notification = $sent;

            return true;
        });

        $this->get(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]))
            ->assertOk()
            ->assertSee('Set a new password');

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('order'));

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_password_reset_request_does_not_reveal_unknown_emails(): void
    {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'missing@example.com'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'If an account matches that email address, a password reset link will be sent.')
            ->assertSessionHasNoErrors();
    }

    public function test_password_reset_works_for_email_addresses_with_different_casing(): void
    {
        $user = User::factory()->create([
            'email' => 'recover@example.com',
            'password' => 'OldPassword123',
        ]);
        $user->forceFill(['email' => 'Recover@Example.com'])->save();
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => ' recover@example.com '])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'If an account matches that email address, a password reset link will be sent.')
            ->assertSessionHasNoErrors();

        $notification = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $sent) use (&$notification): bool {
            $notification = $sent;

            return true;
        });

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => 'recover@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('order'));

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_authenticated_user_can_change_password_only_with_their_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123']);

        $this->actingAs($user)
            ->get(route('profile.password'))
            ->assertOk()
            ->assertSee(route('profile.password.update'), false);

        $this->actingAs($user)
            ->from(route('profile.password'))
            ->post(route('profile.password.update'), [
                'current_password' => 'WrongPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ])
            ->assertRedirect(route('profile.password'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));

        $this->actingAs($user)
            ->from(route('profile.password'))
            ->post(route('profile.password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ])
            ->assertRedirect(route('profile.password'))
            ->assertSessionHas('status', 'Your password has been updated.');

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_forgot_pin_sends_a_one_time_link_and_changes_only_the_pin(): void
    {
        $user = User::factory()->create([
            'email' => 'pin-owner@example.com',
            'password' => 'KeepPassword123',
            'pin_hash' => Hash::make('1234'),
        ]);
        Notification::fake();

        $this->get(route('pin.request'))->assertOk();
        $this->post(route('pin.email'), ['email' => ' PIN-OWNER@example.com '])
            ->assertRedirect()
            ->assertSessionHas('status');

        $notification = null;
        Notification::assertSentTo($user, ResetInvestorPinNotification::class, function (ResetInvestorPinNotification $sent) use (&$notification): bool {
            $notification = $sent;

            return true;
        });

        $resetUrl = route('pin.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]);
        $this->get($resetUrl)->assertOk()->assertSee('Choose a new PIN');

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true, 'pin_verified' => true])
            ->post(route('pin.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'pin' => '2468',
                'pin_confirmation' => '2468',
            ])
            ->assertRedirect(route('investors'))
            ->assertSessionMissing('investor_pin_verified')
            ->assertSessionMissing('pin_verified');

        $this->assertTrue(Hash::check('2468', $user->fresh()->pin_hash));
        $this->assertTrue(Hash::check('KeepPassword123', $user->fresh()->password));
        $this->assertDatabaseMissing('pin_reset_tokens', ['email' => $user->email]);
        $this->get($resetUrl)->assertForbidden();
    }

    public function test_forgot_pin_request_does_not_reveal_unknown_emails(): void
    {
        Notification::fake();

        $this->from(route('pin.request'))
            ->post(route('pin.email'), ['email' => 'missing@example.com'])
            ->assertRedirect(route('pin.request'))
            ->assertSessionHas('status', 'If an account matches that email address, a PIN reset link will be sent.')
            ->assertSessionHasNoErrors();
    }

    public function test_invalid_pin_reset_tokens_cannot_change_the_pin(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->from(route('pin.request'))
            ->post(route('pin.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'pin' => '2468',
                'pin_confirmation' => '2468',
            ])
            ->assertRedirect(route('pin.request'))
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('1234', $user->fresh()->pin_hash));
    }
}
