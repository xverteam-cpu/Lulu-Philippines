<?php

namespace App\Http\Controllers;

use App\Models\IdentityVerification;
use App\Support\IdentityImageQuality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class IdentityVerificationController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.verification', [
            'verification' => $request->user()->identityVerification,
            'idTypes' => IdentityVerification::ID_TYPES,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();
        $existing = $user->identityVerification;

        if ($existing?->status === 'verified') {
            return redirect()->route('profile.verification')
                ->with('status', 'Your identity is already verified.');
        }

        if ($existing?->status === 'pending') {
            return redirect()->route('profile.verification')
                ->with('status', 'Your ID is already under review.');
        }

        $validated = $request->validate([
            'id_type' => ['required', Rule::in(array_keys(IdentityVerification::ID_TYPES))],
            'front_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480', $this->clarityRule()],
            'back_image' => ['nullable', 'required_if:id_type,drivers_license', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480', $this->clarityRule()],
            'selfie_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480', $this->clarityRule()],
            'consent' => ['required', 'accepted'],
        ]);

        $newPaths = [];
        try {
            $frontPath = $this->storeImage($validated['front_image'], $user->id, 'front');
            $newPaths[] = $frontPath;
            $backPath = isset($validated['back_image'])
                ? $this->storeImage($validated['back_image'], $user->id, 'back')
                : null;
            if ($backPath) {
                $newPaths[] = $backPath;
            }
            $selfiePath = $this->storeImage($validated['selfie_image'], $user->id, 'selfie');
            $newPaths[] = $selfiePath;

            DB::transaction(function () use ($user, $validated, $frontPath, $backPath, $selfiePath): void {
                IdentityVerification::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'id_type' => $validated['id_type'],
                        'front_image_path' => $frontPath,
                        'back_image_path' => $backPath,
                        'selfie_image_path' => $selfiePath,
                        'status' => 'pending',
                        'review_note' => null,
                        'reviewed_by' => null,
                        'submitted_at' => now(),
                        'reviewed_at' => null,
                    ]
                );
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newPaths);
            throw $exception;
        }

        if ($existing) {
            Storage::disk('local')->delete(array_filter([
                $existing->front_image_path,
                $existing->back_image_path,
                $existing->selfie_image_path,
            ]));
        }

        return redirect()->route('profile.verification')
            ->with('status', 'Your ID and selfie were submitted securely and are now under review.');
    }

    private function storeImage(UploadedFile $file, int $userId, string $side): string
    {
        $extension = strtolower($file->extension() ?: 'jpg');
        $path = $file->storeAs(
            'identity-verifications/'.$userId,
            $side.'-'.Str::uuid().'.'.$extension,
            'local'
        );

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The identity document could not be stored securely.');
        }

        return $path;
    }

    private function clarityRule(): \Closure
    {
        return static function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value instanceof UploadedFile && ! IdentityImageQuality::isClear($value)) {
                $fail('This photo is blurry, too small, or too large. Retake it in good light with the ID text in focus.');
            }
        };
    }
}
