<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\OrgRegion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Yajra\DataTables\Facades\DataTables;

class MemberActivationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';

        $query = MemberActivation::query()
            ->with(['orgRegion', 'placeOfBirthCity'])
            ->latest('updated_at');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('nim', 'like', $like)
                    ->orWhere('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $members = $query->paginate(15)->withQueryString();

        $filterState = ['q' => $q];

        return view('admin.member-activations.index', compact('members', 'filterState'));
    }

    /** Parameter harus selaras dengan `{member_activation}` di definisi rute (implicit binding). */
    public function edit(MemberActivation $member_activation): View
    {
        $member_activation->load([
            'placeOfBirthCity',
            'orgRegion',
            'media' => fn ($q) => $q->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION),
        ]);
        $provinces = Province::query()->orderBy('name', 'asc')->get();
        $orgRegions = OrgRegion::query()->orderBy('name', 'asc')->get();

        return view('admin.member-activations.edit', [
            'member' => $member_activation,
            'provinces' => $provinces,
            'orgRegions' => $orgRegions,
        ]);
    }

    public function update(UpdateMemberRequest $request, MemberActivation $member_activation): RedirectResponse
    {
        $member_activation->update($request->validatedPersistable());
        $this->attachSupportingDocumentsFromRequest($request, $member_activation);

        return redirect()
            ->back()
            ->with('success', 'Aktivasi Anggota berhasil diperbarui.');
    }

    public function destroy(MemberActivation $member_activation): RedirectResponse
    {
        $member_activation->delete();

        return redirect()
            ->route('admin.member-activations.index')
            ->with('success', 'Aktivasi Anggota berhasil dihapus.');
    }

    /** Hapus satu lampiran koleksi pendukung (hanya milik anggota ini). */
    public function destroySupportingMedia(MemberActivation $member_activation, Media $media): RedirectResponse
    {
        $owned = $member_activation->media()
            ->whereKey($media->getKey())
            ->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION)
            ->first();

        abort_if($owned === null, 404);

        $media->delete();

        return redirect()
            ->route('admin.member-activations.edit', ['member_activation' => $member_activation])
            ->with('success', 'Dokumen pendukung berhasil dihapus.');
    }

    public static function attachSupportingDocumentsFromRequest(Request $request, Member|MemberActivation $member): void
    {
        $files = $request->file('supporting_documents');
        if ($files === null) {
            return;
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $member->addMedia($file)->toMediaCollection(Member::SUPPORTING_DOCUMENTS_COLLECTION);
            }
        }
    }

    public function getSuggestionMember(MemberActivation $member_activation, Request $request)
    {
        $members = Member::query()
        // if search null
        ->when(!$request->input('search.value'), function ($query) use ($member_activation) {
            $query->where('nim', $member_activation->nim);
            $query->orWhere('email', $member_activation->email);
        });

        return DataTables::of($members)
            ->addIndexColumn()
            ->make(true);
    }
}
