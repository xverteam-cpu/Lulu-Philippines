<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create
        {--email= : Email address for the admin account}
        {--username= : Username for the admin account}
        {--name=Administrator : Display name for the admin account}';

    protected $description = 'Create or promote an admin account';

    public function handle(): int
    {
        $email = trim((string) ($this->option('email') ?: $this->ask('Admin email')));
        $username = trim((string) ($this->option('username') ?: $this->ask('Admin username')));
        $name = trim((string) $this->option('name')) ?: 'Administrator';
        $password = (string) $this->secret('Admin password');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please provide a valid email address.');
            return self::FAILURE;
        }

        if ($username === '' || $password === '') {
            $this->error('Username and password are required.');
            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('The password must be at least 8 characters.');
            return self::FAILURE;
        }

        $emailUser = User::where('email', $email)->first();
        $usernameUser = User::where('username', $username)->first();

        if ($emailUser && $usernameUser && $emailUser->isNot($usernameUser)) {
            $this->error('The email and username belong to different users. No changes were made.');
            return self::FAILURE;
        }

        $user = $emailUser ?: $usernameUser;

        DB::transaction(function () use (&$user, $name, $email, $username, $password): void {
            if (! $user) {
                $user = new User;
            }

            $user->forceFill([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'is_admin' => true,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        });

        $this->info(($emailUser || $usernameUser ? 'Admin account updated' : 'Admin account created').'.');
        $this->line("Username: {$username}");
        $this->line("Email: {$email}");

        return self::SUCCESS;
    }
}
