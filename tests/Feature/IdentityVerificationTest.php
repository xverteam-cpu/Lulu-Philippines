<?php

namespace Tests\Feature;

use App\Models\IdentityVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdentityVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_profile_shows_a_clickable_get_verified_badge(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Get verified')
            ->assertSee('href="'.route('profile.verification').'"', false)
            ->assertDontSee('>Online</span>', false)
            ->assertDontSee('>Offline</span>', false);
    }

    public function test_user_can_submit_a_supported_id_privately_for_review(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.verification'))
            ->assertOk()
            ->assertSee('Step 01 of 03')
            ->assertSee('Identity document')
            ->assertSee('Selfie check')
            ->assertSee('Review')
            ->assertSee('Choose file')
            ->assertSee('Use camera')
            ->assertSee('National ID')
            ->assertSee('SSS ID')
            ->assertSee('UMID')
            ->assertSee('Take or upload your selfie')
            ->assertSee('Submit for review')
            ->assertSee(route('profile.verification.submit'))
            ->assertDontSee('Connect the verification endpoint')
            ->assertSee('variance');

        $this->actingAs($user)
            ->post(route('profile.verification.submit'), [
                'id_type' => 'national_id',
                'front_image' => $this->sharpImage('front.png'),
                'back_image' => $this->sharpImage('back.png'),
                'selfie_image' => $this->sharpImage('selfie.png'),
                'consent' => '1',
            ])
            ->assertRedirect(route('profile.verification'))
            ->assertSessionHas('status', 'Your ID and selfie were submitted securely and are now under review.');

        $verification = IdentityVerification::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('pending', $verification->status);
        $this->assertSame('national_id', $verification->id_type);
        Storage::disk('local')->assertExists($verification->front_image_path);
        Storage::disk('local')->assertExists($verification->back_image_path);
        Storage::disk('local')->assertExists($verification->selfie_image_path);
        $this->assertStringStartsWith('identity-verifications/'.$user->id.'/', $verification->front_image_path);
        $this->assertFalse(file_exists(public_path($verification->front_image_path)));
    }

    public function test_blurry_id_photo_is_rejected_by_the_server(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.verification'))
            ->post(route('profile.verification.submit'), [
                'id_type' => 'national_id',
                'front_image' => UploadedFile::fake()->image('blurry.png', 1200, 800),
                'selfie_image' => $this->sharpImage('selfie.png'),
                'consent' => '1',
            ])
            ->assertRedirect(route('profile.verification'))
            ->assertSessionHasErrors('front_image');

        $this->assertDatabaseCount('identity_verifications', 0);
    }

    public function test_submission_requires_supported_id_and_consent(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.verification'))
            ->post(route('profile.verification.submit'), [
                'id_type' => 'unlisted',
                'front_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('profile.verification'))
            ->assertSessionHasErrors(['id_type', 'front_image', 'selfie_image', 'consent']);

        $this->assertDatabaseCount('identity_verifications', 0);
    }

    public function test_admin_can_view_a_pending_id_and_approval_deletes_the_photo(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('id.png', 1200, 800)
            ->storeAs('identity-verifications/'.$user->id, 'front-id.png', 'local');
        $selfiePath = UploadedFile::fake()->image('selfie.png', 1200, 800)
            ->storeAs('identity-verifications/'.$user->id, 'selfie.png', 'local');
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'id_type' => 'umid',
            'front_image_path' => $path,
            'selfie_image_path' => $selfiePath,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'pin_hash' => Hash::make('1234'),
        ]);

        $documentResponse = $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.identity-verifications.document', [$verification, 'front']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $cacheControl = $documentResponse->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringNotContainsString('public', $cacheControl);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.identity-verifications.document', [$verification, 'selfie']))
            ->assertOk();

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->post(route('admin.identity-verifications.approve', $verification))
            ->assertRedirect(route('admin.identity-verifications'))
            ->assertSessionHas('status', 'Identity verification approved. ID and selfie photos were deleted.');

        $verification->refresh();
        $this->assertSame('verified', $verification->status);
        $this->assertSame($admin->id, $verification->reviewed_by);
        $this->assertNull($verification->front_image_path);
        $this->assertNull($verification->selfie_image_path);
        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertMissing($selfiePath);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Verified')
            ->assertSee('href="'.route('profile.verification').'"', false);
    }

    public function test_admin_rejection_deletes_images_and_allows_user_to_resubmit(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('id.png', 1200, 800)
            ->storeAs('identity-verifications/'.$user->id, 'front-id.png', 'local');
        $selfiePath = UploadedFile::fake()->image('selfie.png', 1200, 800)
            ->storeAs('identity-verifications/'.$user->id, 'selfie.png', 'local');
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'id_type' => 'drivers_license',
            'front_image_path' => $path,
            'selfie_image_path' => $selfiePath,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->post(route('admin.identity-verifications.reject', $verification), [
                'review_note' => 'The image is blurry. Please retake it.',
            ])
            ->assertRedirect(route('admin.identity-verifications'));

        $verification->refresh();
        $this->assertSame('rejected', $verification->status);
        $this->assertSame('The image is blurry. Please retake it.', $verification->review_note);
        $this->assertNull($verification->front_image_path);
        $this->assertNull($verification->selfie_image_path);
        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertMissing($selfiePath);

        $this->actingAs($user)
            ->get(route('profile.verification'))
            ->assertOk()
            ->assertSee('Your previous submission was not approved.')
            ->assertSee('The image is blurry. Please retake it.')
            ->assertSee('name="front_image"', false);
    }

    public function test_only_an_admin_with_verified_session_pin_can_access_review_documents(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('id.png', 1200, 800)
            ->storeAs('identity-verifications/'.$user->id, 'front-id.png', 'local');
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'id_type' => 'sss',
            'front_image_path' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.identity-verifications.document', [$verification, 'front']))
            ->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => false])
            ->get(route('admin.identity-verifications.document', [$verification, 'front']))
            ->assertRedirect(route('investors'));
    }

    private function sharpImage(string $name): UploadedFile
    {
        $image = imagecreatetruecolor(1200, 800);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        for ($x = 0; $x < 1200; $x += 12) {
            imageline($image, $x, 0, $x, 799, $black);
        }

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return UploadedFile::fake()->createWithContent($name, $contents);
    }
}
