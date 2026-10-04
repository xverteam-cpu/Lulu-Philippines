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
            $this->assertStringContainsString('Welcome to Lulu, <strong>Jane Doe</strong>.', $html);
            $this->assertStringContainsString('successfully registered', $html);
            $this->assertStringContainsString('What’s Next?', $html);
            $this->assertStringContainsString('Silver Package', $html);
            $this->assertStringContainsString('Gold Package', $html);
            $this->assertStringContainsString('Platinum Package', $html);
            $this->assertStringContainsString('href="'.route('invest').'"', $html);
            $this->assertStringNotContainsString('Lulu Holdings Corp.', $html);
            $this->assertStringNotContainsString('Suite 123, 123 Anywhere St.', $html);
            $this->assertStringNotContainsString('Hi Pete', $html);
            $this->assertStringNotContainsString('journalism worth reading', $html);
            $this->assertSame(4, preg_match_all('/src="data:image\\/jpeg;base64,/', $html));

            return true;
        });
    }
}
