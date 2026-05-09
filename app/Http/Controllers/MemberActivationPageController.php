<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberActivationRequest;
use App\Models\City;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\MemberActivationEmailOtpVerification;
use App\Models\OrgRegion;
use App\Notifications\MemberActivationEmailVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;

class MemberActivationPageController extends Controller
{
    public function index(Request $request): View
    {
        $provinces = Province::query()->orderBy('name', 'asc')->get();
        $orgRegions = OrgRegion::query()->orderBy('name', 'asc')->get();

        $placeCode = old('place_of_birth_code', '');
        $placeName = '';
        if ($placeCode !== '') {
            $placeRow = City::query()->where('code', $placeCode)->first();
            $placeName = $placeRow?->name ?? '';
        }

        $existingSupportingCount = 0;
        $maxNewSupportingFiles = max(
            0,
            min(
                Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT,
                Member::SUPPORTING_DOCUMENTS_MAX_TOTAL - $existingSupportingCount,
            ),
        );
        $supportingMaxFileMb = max(1, (int) ceil(config('media-library.max_file_size') / 1024 / 1024));
        $supportingAccept = Member::supportingDocumentFileInputAccept();
        $supportingAcceptedDropzone = Member::supportingDocumentDropzoneAcceptedFiles();

        $errorBag = $request->session()->get('errors');
        if (! $errorBag instanceof ViewErrorBag) {
            $errorBag = new ViewErrorBag;
        }
        $supportingDocsHasError =
            $errorBag->has('supporting_documents')
            || collect($errorBag->keys())->contains(fn ($key) => str_starts_with((string) $key, 'supporting_documents.'));

        return view('front.about.member-activation', compact(
            'provinces',
            'orgRegions',
            'placeCode',
            'placeName',
            'maxNewSupportingFiles',
            'supportingMaxFileMb',
            'supportingAccept',
            'supportingAcceptedDropzone',
            'supportingDocsHasError',
        ));
    }

    public function store(StoreMemberActivationRequest $request): RedirectResponse
    {
        $memberActivation = MemberActivation::query()->updateOrCreate(
            ['email' => $request->email],
            $request->validatedPersistable()
        );
        MemberController::attachSupportingDocumentsFromRequest($request, $memberActivation);

        return redirect()
            ->back()
            ->with('success', 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari pihak kami.');
    }

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $otp = rand(100000, 999999);

        // Search by email only, update or create with new OTP
        $member_activation_email_otp_verification = MemberActivationEmailOtpVerification::updateOrCreate(
            ['email' => $request->email],        // search criteria
            ['otp' => $otp]                      // values to set
        );

        Notification::send(
            $member_activation_email_otp_verification,
            new MemberActivationEmailVerification($member_activation_email_otp_verification->toArray())
        );

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi berhasil dikirim. Silakan cek email Anda untuk melakukan verifikasi.',
        ], Response::HTTP_OK);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $member_activation_email_otp_verification = MemberActivationEmailOtpVerification::query()
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();
        if (! $member_activation_email_otp_verification) {
            return response()->json([
                'success' => false,
                'message' => 'OTP tidak valid.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $member_activation_email_otp_verification->verified_at = now();
        $member_activation_email_otp_verification->save();

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi.',
        ], 200);
    }
}
