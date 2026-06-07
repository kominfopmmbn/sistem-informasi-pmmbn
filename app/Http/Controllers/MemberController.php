<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\MemberActivation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'verification' => ['nullable', Rule::in(['verified', 'unverified'])],
        ]);

        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';
        $verification = $filters['verification'] ?? null;

        $query = Member::query()
            ->with(['placeOfBirthCity', 'kta', 'college'])
            ->latest('updated_at');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('nim', 'like', $like)
                    ->orWhere('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        if ($verification === 'verified') {
            $query->whereHas('kta');
        } elseif ($verification === 'unverified') {
            $query->whereDoesntHave('kta');
        }

        $members = $query->paginate(15)->withQueryString();

        $filterState = ['q' => $q, 'verification' => $verification];

        return view('admin.members.index', compact('members', 'filterState'));
    }

    public function create(): View
    {
        $provinces = Province::query()->orderBy('name', 'asc')->get();
        return view('admin.members.create', compact('provinces'));
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = Member::query()->create($request->validatedPersistable());
        $this->attachSupportingDocumentsFromRequest($request, $member);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member): View
    {
        $member->load([
            'placeOfBirthCity',
            'village.district.city.province',
            'college.city',
            'college.province',
            'media' => fn ($q) => $q->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION),
        ]);
        $provinces = Province::query()->orderBy('name', 'asc')->get();

        return view('admin.members.edit', compact('member', 'provinces'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validatedPersistable());
        $this->attachSupportingDocumentsFromRequest($request, $member);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->deleteOrFail();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /** Hapus satu lampiran koleksi pendukung (hanya milik anggota ini). */
    public function destroySupportingMedia(Member $member, Media $media): RedirectResponse
    {
        $owned = $member->media()
            ->whereKey($media->getKey())
            ->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION)
            ->first();

        abort_if($owned === null, 404);

        $media->delete();

        return redirect()
            ->route('admin.members.edit', $member)
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
}
