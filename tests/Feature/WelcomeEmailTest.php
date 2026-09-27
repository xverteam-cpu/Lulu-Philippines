<?php

namespace Tests\Feature;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_receive_a_welcome_email(): void
    {
        Mail::fake();

        $response = $this->post(route('register.partner'), [
            'fullname' => 'Jane Doe',
            'username' => 'janedoe',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'referral' => '',
        ]);

        $response->assertRedirect();

        $user = User::where('email', 'jane@example.com')->firstOrFail();

        Mail::assertSent(WelcomeEmail::class, function (WelcomeEmail $mail) use ($user): bool {
            $html = $mail->render();

            $this->assertTrue($mail->hasTo($user->email));
            $this->assertStringContainsString('Welcome to Lulu, <strong>Jane Doe</strong>', $html);
            $this->assertStringContainsString('Your Lulu Account', $html);
            $this->assertStringContainsString('SILVER PLAN', $html);
            $this->assertStringContainsString('GOLD PLAN', $html);
            $this->assertStringContainsString('PLATINUM PLAN', $html);
            $this->assertStringContainsString('luluphilippines@gmail.com', $html);
            $this->assertStringNotContainsString('Lulu Holdings Corp.', $html);
            $this->assertStringNotContainsString('Suite 123, 123 Anywhere St.', $html);
            $this->assertStringNotContainsString('Welcome to Lulu, {{ $user_name }}', $html);
            $this->assertSame(5, preg_match_all('/data:image\\/(?:jpeg|png);base64,/', $html));

            return true;
        });
    }
}
