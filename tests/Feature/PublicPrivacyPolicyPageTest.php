<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPrivacyPolicyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_is_public_and_serves_the_supplied_document(): void
    {
        $this->get(route('privacy-policy'))
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSee('Privacy Policy | LuLu Philippines')
            ->assertSee('Last updated: 17 October 2026')
            ->assertSee('About this policy')
            ->assertSee('Your choices &amp; rights', false)
            ->assertSee('National Privacy Commission');
    }

    public function test_public_pages_link_to_the_privacy_policy(): void
    {
        $this->get(route('public.home'))
            ->assertOk()
            ->assertSee(route('privacy-policy'), false)
            ->assertSee(route('terms-of-service'), false);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee(route('privacy-policy'), false)
            ->assertSee(route('terms-of-service'), false);
    }

    public function test_terms_of_service_is_public_and_serves_the_supplied_document(): void
    {
        $this->get(route('terms-of-service'))
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSee('Terms of Service | LuLu Philippines')
            ->assertSee('Last updated: 17 October 2026')
            ->assertSee('Terms of service.')
            ->assertSee('Acceptable use')
            ->assertSee('Contact us');
    }
}
