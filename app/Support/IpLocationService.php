<?php

namespace App\Support;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Support\Facades\Log;
use MaxMind\Db\Reader\InvalidDatabaseException;

class IpLocationService
{
    public function lookup(string $ipAddress, int $userId): ?string
    {
        if (filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false) {
            return null;
        }

        $databasePath = config('services.geoip.database_path');
        if (! is_string($databasePath) || trim($databasePath) === '' || ! is_file($databasePath)) {
            Log::notice('Local GeoIP database is not configured or could not be found.', [
                'user_id' => $userId,
            ]);

            return null;
        }

        try {
            $reader = new Reader($databasePath);
            try {
                $location = $reader->city($ipAddress);
            } finally {
                $reader->close();
            }
        } catch (AddressNotFoundException) {
            return null;
        } catch (InvalidDatabaseException|\InvalidArgumentException $exception) {
            Log::warning('Local IP geolocation lookup failed.', [
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        $parts = collect([
            $location->city->name,
            $location->mostSpecificSubdivision->name,
            $location->country->name,
        ])
            ->filter(fn ($part) => is_string($part) && trim($part) !== '')
            ->map(fn (string $part) => trim($part))
            ->unique()
            ->values();

        return $parts->isEmpty() ? null : $parts->implode(', ');
    }
}
