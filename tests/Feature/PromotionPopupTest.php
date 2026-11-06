<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_see_the_daily_promotion_popup_with_the_supplied_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="promotionOverlay"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-label="Close promotion"', false)
            ->assertSee('src="'.asset('Promotions.png').'"', false)
            ->assertSee('lulu-promotion-shown-user-'.$user->id)
            ->assertSee('width:90vw; max-width:400px;', false)
            ->assertSee('max-height:80vh; max-height:80dvh;', false)
            ->assertSee('object-fit:contain;', false)
            ->assertSee("event.key === 'Escape'", false);

        $this->assertFileExists(public_path('Promotions.png'));
    }
}
