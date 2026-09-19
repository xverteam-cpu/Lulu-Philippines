<?php

namespace App\Support;

use App\Models\PackageSlot;

class InvestmentPackages
{
    /**
     * @return array<string, array{name: string, price: float, daily_interest_rate: float, duration_days: int, min_amount: float, max_amount: float}>
     */
    public static function all(): array
    {
        return [
            'crunch' => [
                'name' => 'Silver',
                'price' => 129.00,
                'daily_interest_rate' => 0.70,
                'duration_days' => 150,
                'min_amount' => 129.00,
                'max_amount' => 798.99,
            ],
            'loaded' => [
                'name' => 'Gold',
                'price' => 799.00,
                'daily_interest_rate' => 0.80,
                'duration_days' => 120,
                'min_amount' => 799.00,
                'max_amount' => 7998.99,
            ],
            'supreme' => [
                'name' => 'Platinum',
                'price' => 7999.00,
                'daily_interest_rate' => 0.90,
                'duration_days' => 90,
                'min_amount' => 7999.00,
                'max_amount' => 50000.00,
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    public static function defaults(): array
    {
        return [
            'crunch' => 250,
            'loaded' => 250,
            'supreme' => 250,
        ];
    }

    public static function currentSlots(): array
    {
        $slots = PackageSlot::query()
            ->pluck('remaining_slots', 'package_key')
            ->toArray();

        foreach (self::defaults() as $key => $default) {
            if (! isset($slots[$key])) {
                $slots[$key] = $default;
            }
        }

        return $slots;
    }

    public static function setRemainingSlots(string $key, int $remainingSlots): void
    {
        if (! array_key_exists($key, self::defaults())) {
            return;
        }

        PackageSlot::updateOrCreate(
            ['package_key' => $key],
            ['remaining_slots' => max(0, $remainingSlots)]
        );
    }

    public static function reserveSlot(string $key): bool
    {
        $default = self::defaults()[$key] ?? 250;
        $slot = PackageSlot::firstOrCreate([
            'package_key' => $key,
        ], [
            'remaining_slots' => $default,
        ]);

        if ($slot->remaining_slots <= 0) {
            return false;
        }

        return (bool) $slot->decrement('remaining_slots');
    }
}
