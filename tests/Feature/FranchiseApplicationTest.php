<?php

namespace Tests\Feature;

use App\Models\FranchiseApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FranchiseApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_franchise_page_displays_a_real_application_form(): void
    {
        $this->get(route('franchising', ['package' => '60']))
            ->assertOk()
            ->assertSee('id="franchise-application"', false)
            ->assertSee('name="full_name"', false)
            ->assertSee('name="preferred_package"', false)
            ->assertSee('<option value="60" selected>', false)
            ->assertSee('Submit Application');
    }

    public function test_guest_can_submit_an_application_and_admin_can_review_it(): void
    {
        $this->post(route('franchise-applications.store'), $this->validApplication())
            ->assertRedirect(route('franchising').'#franchise-application')
            ->assertSessionHas('status');

        $application = FranchiseApplication::query()->firstOrFail();

        $this->assertDatabaseHas('franchise_applications', [
            'id' => $application->id,
            'user_id' => null,
            'full_name' => 'Jane Franchise Applicant',
            'email' => 'jane@example.com',
            'phone_number' => '+639171234567',
            'preferred_package' => '40',
            'location' => 'Makati City',
            'business_background' => 'I have operated local restaurants.',
            'investment_capacity' => '₱25 million',
            'additional_notes' => 'Please contact me in the morning.',
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('admin.franchises').'"', false)
            ->assertSee('>Franchise</span>', false);

        $this->actingAs($admin)
            ->get(route('admin.franchises'))
            ->assertOk()
            ->assertSee('>Franchise</span>', false)
            ->assertSee('Jane Franchise Applicant')
            ->assertSee(route('admin.franchises.show', $application), false);

        $this->get(route('admin.franchises.show', $application))
            ->assertOk()
            ->assertSee('jane@example.com')
            ->assertSee('Makati City')
            ->assertSee('I have operated local restaurants.')
            ->assertSee('₱25 million')
            ->assertSee('Please contact me in the morning.');
    }

    public function test_signed_in_applicant_is_linked_to_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('franchise-applications.store'), $this->validApplication())
            ->assertRedirect(route('franchising').'#franchise-application');

        $this->assertDatabaseHas('franchise_applications', [
            'user_id' => $user->id,
            'email' => 'jane@example.com',
        ]);
    }

    public function test_invalid_application_is_not_saved(): void
    {
        $this->from(route('franchising'))
            ->post(route('franchise-applications.store'), [
                ...$this->validApplication(),
                'preferred_package' => '100',
                'email' => 'not-an-email',
            ])
            ->assertSessionHasErrors(['preferred_package', 'email']);

        $this->assertDatabaseCount('franchise_applications', 0);
    }

    public function test_only_admins_can_access_franchise_applications(): void
    {
        $application = FranchiseApplication::create($this->validApplication());

        $this->get(route('admin.franchises'))->assertRedirect(route('login'));
        $this->get(route('admin.franchises.show', $application))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.franchises'))
            ->assertForbidden();

        $this->get(route('admin.franchises.show', $application))
            ->assertForbidden();
    }

    /**
     * @return array<string, string>
     */
    private function validApplication(): array
    {
        return [
            'full_name' => 'Jane Franchise Applicant',
            'email' => 'jane@example.com',
            'phone_number' => '+639171234567',
            'preferred_package' => '40',
            'location' => 'Makati City',
            'business_background' => 'I have operated local restaurants.',
            'investment_capacity' => '₱25 million',
            'additional_notes' => 'Please contact me in the morning.',
        ];
    }
}
