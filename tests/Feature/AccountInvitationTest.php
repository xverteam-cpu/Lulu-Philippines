<?php

namespace Tests\Feature;

use App\Mail\AccountSetupInvitation;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_email_a_private_setup_link_to_a_client(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $referrer = User::factory()->create(['name' => 'Wilmer Pasco']);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->post(route('admin.account-invitations.store'), [
                'name' => 'Client Name',
                'email' => ' CLIENT@example.com ',
                'referrer_id' => $referrer->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $invitation = AccountInvitation::firstOrFail();
        $this->assertSame('client@example.com', $invitation->email);
        $this->assertSame($admin->id, $invitation->invited_by);
        $this->assertTrue($invitation->expires_at->isFuture());
        $this->assertNotSame('client@example.com', $invitation->token_hash);

        Mail::assertSent(AccountSetupInvitation::class, function (AccountSetupInvitation $mail) use ($invitation): bool {
            $token = basename((string) parse_url($mail->setupLink, PHP_URL_PATH));

            return $mail->hasTo('client@example.com')
                && $mail->recipientName === 'Client Name'
                && hash('sha256', $token) === $invitation->token_hash;
        });
    }

    public function test_non_admin_cannot_send_account_invitations(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->post(route('admin.account-invitations.store'), [
                'name' => 'Client Name',
                'email' => 'client@example.com',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('account_invitations', 0);
        Mail::assertNothingSent();
    }

    public function test_admin_dashboard_exposes_the_invitation_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Send setup link')
            ->assertSee('id="account-invitation-panel"', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee(route('admin.account-invitations.store'), false)
            ->assertSee('Referrer');
    }

    public function test_invited_client_can_set_password_and_pin_once(): void
    {
        $token = 'one-time-private-token';
        $invitation = AccountInvitation::create([
            'name' => 'Client Name',
            'email' => 'client@example.com',
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
            'invited_by' => User::factory()->create(['is_admin' => true])->id,
            'referrer_id' => User::factory()->create(['name' => 'Wilmer Pasco'])->id,
        ]);

        $this->get(route('account-setup.show', ['token' => $token]))
            ->assertOk()
            ->assertSee('Client Name')
            ->assertSee('4-digit PIN');

        $this->post(route('account-setup.store', ['token' => $token]), [
            'password' => 'A-secure-password-123',
            'password_confirmation' => 'A-secure-password-123',
            'pin' => '0427',
            'pin_confirmation' => '0427',
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'client@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('A-secure-password-123', $user->password));
        $this->assertTrue(Hash::check('0427', $user->pin_hash));
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->pin_set_at);
        $this->assertSame($invitation->referrer_id, $user->referred_by);
        $this->assertNotNull($invitation->fresh()->accepted_at);

        $this->get(route('account-setup.show', ['token' => $token]))->assertNotFound();
    }

    public function test_expired_invitation_cannot_be_used(): void
    {
        $token = 'expired-private-token';
        AccountInvitation::create([
            'name' => 'Client Name',
            'email' => 'expired@example.com',
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->subMinute(),
            'invited_by' => User::factory()->create(['is_admin' => true])->id,
        ]);

        $this->get(route('account-setup.show', ['token' => $token]))->assertNotFound();
        $this->post(route('account-setup.store', ['token' => $token]), [
            'password' => 'A-secure-password-123',
            'password_confirmation' => 'A-secure-password-123',
            'pin' => '0427',
            'pin_confirmation' => '0427',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'expired@example.com']);
    }

    public function test_invitation_requires_a_referrer(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->post(route('admin.account-invitations.store'), [
                'name' => 'Client Name',
                'email' => 'client@example.com',
            ])
            ->assertSessionHasErrors('referrer_id');

        $this->assertDatabaseCount('account_invitations', 0);
        Mail::assertNothingSent();
    }
}
