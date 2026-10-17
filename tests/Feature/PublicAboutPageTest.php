<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_is_public_and_links_to_existing_public_sections(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Get to know us')
            ->assertSee('no account required to browse')
            ->assertSee(route('investors'), false)
            ->assertSee(route('franchising'), false)
            ->assertSee(route('public.home'), false)
            ->assertSee('Legalities')
            ->assertSee('Business Permit')
            ->assertSee('BIR Registration Certificate')
            ->assertSee('Barangay Business Clearance')
            ->assertSee('Permit to Offer Securities for Sale')
            ->assertSee(asset('BusinessPermit.png'), false)
            ->assertSee(asset('BIR Final.png'), false)
            ->assertSee(asset('BarangayCert.png'), false)
            ->assertSee(asset('Lulucerttosell.png'), false);
    }

    public function test_homepage_links_to_the_about_page(): void
    {
        $this->get(route('public.home'))
            ->assertOk()
            ->assertSee('About Us')
            ->assertSee(route('about'), false);
    }
}
