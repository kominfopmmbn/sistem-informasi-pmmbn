<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberActivationRequest;
use App\Models\City;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\OrgRegion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $memberActivation = MemberActivation::query()->create($request->validatedPersistable());
        MemberController::attachSupportingDocumentsFromRequest($request, $memberActivation);

        return redirect()
            ->back()
            ->with('success', 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari pihak kami.');
    }
}
