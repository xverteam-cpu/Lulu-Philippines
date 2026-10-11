<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IdentityVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()?->is_admin, 403);

            return $next($request);
        });
    }

    public function index(): View
    {
        $verifications = IdentityVerification::query()
            ->with('user')
            ->where('status', 'pending')
            ->orderBy('submitted_at')
            ->paginate(20);

        return view('admin.identity-verifications', [
            'verifications' => $verifications,
            'idTypes' => IdentityVerification::ID_TYPES,
        ]);
    }

    public function document(IdentityVerification $verification, string $side): BinaryFileResponse
    {
        abort_unless(in_array($side, ['front', 'back', 'selfie'], true), 404);

        $path = match ($side) {
            'front' => $verification->front_image_path,
            'back' => $verification->back_image_path,
            'selfie' => $verification->selfie_image_path,
        };
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $filePath = Storage::disk('local')->path($path);
        $mimeType = Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';

        $response = response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="identity-verification.'.$side.'"',
        ]);
        $response->headers->removeCacheControlDirective('public');

        return $response;
    }

    public function approve(Request $request, IdentityVerification $verification): RedirectResponse
    {
        abort_unless($verification->status === 'pending', 409);

        $paths = $this->completeReview($request, $verification, 'verified', null);
        $this->deleteReviewedDocuments($paths, $verification);

        return redirect()->route('admin.identity-verifications')
            ->with('status', 'Identity verification approved. ID and selfie photos were deleted.');
    }

    public function reject(Request $request, IdentityVerification $verification): RedirectResponse
    {
        abort_unless($verification->status === 'pending', 409);
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'max:500'],
        ]);

        $paths = $this->completeReview($request, $verification, 'rejected', $validated['review_note']);
        $this->deleteReviewedDocuments($paths, $verification);

        return redirect()->route('admin.identity-verifications')
            ->with('status', 'Identity verification rejected. The user may submit a new ID and selfie; reviewed photos were deleted.');
    }

    private function completeReview(Request $request, IdentityVerification $verification, string $status, ?string $note): array
    {
        $paths = array_filter([
            $verification->front_image_path,
            $verification->back_image_path,
            $verification->selfie_image_path,
        ]);

        DB::transaction(function () use ($request, $verification, $status, $note): void {
            $verification->update([
                'status' => $status,
                'review_note' => $note,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'front_image_path' => null,
                'back_image_path' => null,
                'selfie_image_path' => null,
            ]);
        });

        return $paths;
    }

    private function deleteReviewedDocuments(array $paths, IdentityVerification $verification): void
    {
        foreach ($paths as $path) {
            if (Storage::disk('local')->exists($path) && ! Storage::disk('local')->delete($path)) {
                Log::error('Reviewed identity document could not be deleted.', [
                    'identity_verification_id' => $verification->id,
                ]);

                throw new RuntimeException('The review was saved, but an ID photo could not be deleted. Please contact support.');
            }
        }
    }
}
