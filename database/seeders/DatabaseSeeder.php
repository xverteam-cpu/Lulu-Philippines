<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Investment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Default test users
        $this->ensureUser('testuser', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Admin account
        $this->ensureUser('admin', [
            'name' => 'Admin',
            'email' => 'admin@lotteria.test',
            'password' => 'adminpassword',
            'is_admin' => true,
        ]);

        // Bello account (can login with email/password)
        $bello = $this->ensureUser('bello', [
            'name' => 'Bello',
            'email' => 'bello@example.com',
            'password' => 'bello1234',
        ]);

        // A sample referral created by Bello
        $referred = $this->ensureUser('refuser', [
            'name' => 'Referred User',
            'email' => 'refuser@example.com',
            'referred_by' => $bello->id,
        ]);

        // Demo investments for Bello
        if ($bello) {
            Investment::create([
                'user_id' => $bello->id,
                'package_key' => 'starter',
                'package_name' => 'Starter Pack',
                'package_price' => 100.00,
                'amount' => 100.00,
                'daily_interest_rate' => 0.50,
                'duration_days' => 30,
                'starts_at' => now()->subDays(10),
            ]);

            Investment::create([
                'user_id' => $bello->id,
                'package_key' => 'growth',
                'package_name' => 'Growth Pack',
                'package_price' => 500.00,
                'amount' => 500.00,
                'daily_interest_rate' => 0.35,
                'duration_days' => 60,
                'starts_at' => now()->subDays(5),
            ]);
        }

        // Make the referred user buy shares for the referral demo
        if (isset($referred)) {
            Investment::create([
                'user_id' => $referred->id,
                'package_key' => 'starter',
                'package_name' => 'Starter Pack',
                'package_price' => 100.00,
                'amount' => 200.00,
                'daily_interest_rate' => 0.50,
                'duration_days' => 30,
                'starts_at' => now(),
                'status' => 'approved',
            ]);
        }
    }

    private function ensureUser(string $username, array $attributes): User
    {
        return User::updateOrCreate(
            ['username' => $username],
            array_merge([
                'email_verified_at' => now(),
                'password' => 'password',
            ], $attributes)
        );
    }
}
