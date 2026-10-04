<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_page_uses_the_new_layout_and_shows_live_balance_without_fabricated_recipients(): void
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
            ->assertSee('Recipient Wallet ID')
            ->assertSee('Recent recipients')
            ->assertSee('Wallet transfers are not available yet.')
            ->assertDontSee('John M.')
            ->assertDontSee('LW-84392017');

        $this->assertStringContainsString('$284.77', html_entity_decode(strip_tags($response->getContent())));
    }
}
