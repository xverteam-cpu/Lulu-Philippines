<?php

namespace Tests\Feature;

use App\Models\AccountActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_page_uses_the_new_layout_and_shows_live_balance_without_fabricated_transfers(): void
    {
        $user = User::factory()->create([
            'balance' => 284.77,
        ]);

        $response = $this->actingAs($user)
            ->get(route('send'))
            ->assertOk()
            ->assertSee('<html lang="en" class="send-page">', false)
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"', false)
            ->assertSee('Transfer details')
            ->assertSee('Recipient Wallet ID (full name or email)')
            ->assertSee('Recent transfers')
            ->assertSee('No transfers yet.')
            ->assertSee('Funds are deducted from your available balance and credited to the recipient immediately.')
            ->assertDontSee('John M.')
            ->assertDontSee('LW-84392017');

        $this->assertStringContainsString('$284.77', html_entity_decode(strip_tags($response->getContent())));
    }

    public function test_user_can_send_funds_to_another_user_by_email(): void
    {
        $sender = User::factory()->create(['balance' => 100]);
        $recipient = User::factory()->create([
            'name' => 'Recipient User',
            'email' => 'recipient@example.com',
            'balance' => 5,
        ]);

        $this->actingAs($sender)
            ->post(route('send.transfer'), [
                'recipient' => ' RECIPIENT@example.com ',
                'amount' => '25.50',
                'message' => 'Lunch',
            ])
            ->assertRedirect(route('send'))
            ->assertSessionHas('status', 'Sent $25.50 to Recipient User.');

        $this->assertSame(74.5, (float) $sender->fresh()->balance);
        $this->assertSame(30.5, (float) $recipient->fresh()->balance);
        $this->assertDatabaseHas('account_activities', [
            'user_id' => $sender->id,
            'type' => 'funds_sent',
            'amount' => 25.5,
        ]);
        $this->assertDatabaseHas('account_activities', [
            'user_id' => $recipient->id,
            'type' => 'funds_received',
            'amount' => 25.5,
        ]);
        $this->assertSame(2, AccountActivity::query()->count());

        $this->actingAs($sender)
            ->get(route('send'))
            ->assertOk()
            ->assertSee('Funds sent')
            ->assertSee('You sent $25.50 to Recipient User. Message: Lunch');
    }

    public function test_user_can_send_funds_to_another_user_by_full_name(): void
    {
        $sender = User::factory()->create(['balance' => 40]);
        $recipient = User::factory()->create([
            'name' => 'Recipient User',
            'balance' => 0,
        ]);

        $this->actingAs($sender)
            ->post(route('send.transfer'), [
                'recipient' => '  recipient user  ',
                'amount' => '10.00',
            ])
            ->assertRedirect(route('send'))
            ->assertSessionHas('status');

        $this->assertSame(30.0, (float) $sender->fresh()->balance);
        $this->assertSame(10.0, (float) $recipient->fresh()->balance);
    }

    public function test_transfer_rejects_ambiguous_names_self_transfers_and_insufficient_balance(): void
    {
        $sender = User::factory()->create([
            'name' => 'Sender User',
            'balance' => 20,
        ]);
        User::factory()->count(2)->create(['name' => 'Same Recipient']);

        $this->actingAs($sender)
            ->from(route('send'))
            ->post(route('send.transfer'), [
                'recipient' => 'Same Recipient',
                'amount' => '5.00',
            ])
            ->assertRedirect(route('send'))
            ->assertSessionHasErrors('recipient');

        $this->from(route('send'))
            ->post(route('send.transfer'), [
                'recipient' => 'sender user',
                'amount' => '5.00',
            ])
            ->assertRedirect(route('send'))
            ->assertSessionHasErrors('recipient');

        $this->from(route('send'))
            ->post(route('send.transfer'), [
                'recipient' => User::factory()->create()->email,
                'amount' => '20.01',
            ])
            ->assertRedirect(route('send'))
            ->assertSessionHasErrors('amount');

        $this->assertSame(20.0, (float) $sender->fresh()->balance);
        $this->assertSame(0, AccountActivity::query()->count());
    }
}
