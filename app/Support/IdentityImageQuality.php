<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class IdentityImageQuality
{
    private const MIN_SHORT_SIDE = 350;
    private const MIN_LONG_SIDE = 600;
    private const MAX_PIXELS = 12_000_000;
    private const BLUR_VARIANCE_THRESHOLD = 28.0;

    public static function isClear(UploadedFile $file): bool
    {
        if (! function_exists('imagecreatefromstring')) {
            throw new RuntimeException('The GD image extension is required to validate ID photo clarity.');
        }

        if (! $file->isValid()
            || $file->getSize() > 10 * 1024 * 1024
            || ! str_starts_with((string) $file->getMimeType(), 'image/')) {
            return false;
        }

        $dimensions = @getimagesize($file->getPathname());
        if ($dimensions === false) {
            return false;
        }

        [$width, $height] = $dimensions;
        if (min($width, $height) < self::MIN_SHORT_SIDE
            || max($width, $height) < self::MIN_LONG_SIDE
            || $width * $height > self::MAX_PIXELS) {
            return false;
        }

        $contents = file_get_contents($file->getPathname());
        if ($contents === false) {
            throw new RuntimeException('The uploaded ID photo could not be read for quality validation.');
        }

        $source = @imagecreatefromstring($contents);
        if ($source === false) {
            return false;
        }

        $scale = min(1, 360 / max($width, $height));
        $scaledWidth = max(1, (int) round($width * $scale));
        $scaledHeight = max(1, (int) round($height * $scale));
        $scaled = imagecreatetruecolor($scaledWidth, $scaledHeight);
        imagecopyresampled($scaled, $source, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $width, $height);
        imagedestroy($source);

        $gray = [];
        for ($y = 0; $y < $scaledHeight; $y++) {
            for ($x = 0; $x < $scaledWidth; $x++) {
                $color = imagecolorat($scaled, $x, $y);
                $red = ($color >> 16) & 0xff;
                $green = ($color >> 8) & 0xff;
                $blue = $color & 0xff;
                $gray[$y * $scaledWidth + $x] = 0.299 * $red + 0.587 * $green + 0.114 * $blue;
            }
        }
        imagedestroy($scaled);

        $sum = 0.0;
        $sumSquares = 0.0;
        $samples = 0;
        for ($y = 1; $y < $scaledHeight - 1; $y++) {
            for ($x = 1; $x < $scaledWidth - 1; $x++) {
                $index = $y * $scaledWidth + $x;
                $laplacian = $gray[$index - $scaledWidth] + $gray[$index - 1]
                    - 4 * $gray[$index] + $gray[$index + 1]
                    + $gray[$index + $scaledWidth];
                $sum += $laplacian;
                $sumSquares += $laplacian * $laplacian;
                $samples++;
            }
        }

        $variance = $samples > 0
            ? $sumSquares / $samples - ($sum / $samples) ** 2
            : 0;

        return $variance >= self::BLUR_VARIANCE_THRESHOLD;
    }
}
