<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\MemberActivationStatus;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\OrgRegion;
use App\Notifications\MemberActivationAccepted;
use App\Notifications\MemberActivationRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
            ->with(['orgRegion', 'placeOfBirthCity', 'currentStatus'])
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
        $member_activation->query()->delete();

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
            // ->route('admin.member-activations.edit', ['member_activation' => $member_activation])
            ->back()
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
            ->with(['orgRegion'])
            ->when(! $request->input('search.value'), function ($query) use ($member_activation) {
                $query->where('nim', $member_activation->nim);
                $query->orWhere('email', $member_activation->email);
            });

        return DataTables::of($members)
            ->addIndexColumn()
            ->editColumn('date_of_birth', function ($member) {
                return $member->date_of_birth ? Carbon::parse($member->date_of_birth)->format('d-m-Y') : '—';
            })
            ->editColumn('gender_id', function ($member) {
                return $member->gender_id?->label() ?? '—';
            })
            ->make(true);
    }

    public function accept(MemberActivation $member_activation, Request $request): RedirectResponse
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:255'],
            'member_id' => ['nullable', 'exists:members,id'],
        ]);

        DB::beginTransaction();
        if ($request->has('member_id')) {
            $member = Member::query()->findOrFail($request->input('member_id'));
            // update member
            $member->update([
                'nim' => $member_activation->nim,
                'full_name' => $member_activation->full_name,
                'nickname' => $member_activation->nickname,
                'email' => $member_activation->email,
                'place_of_birth_code' => $member_activation->place_of_birth_code,
                'date_of_birth' => $member_activation->date_of_birth,
                'gender_id' => $member_activation->gender_id,
                'org_region_id' => $member_activation->org_region_id,
                'phone_number' => $member_activation->phone_number,
                'member_activation_id' => $member_activation->id,
            ]);
        } else {
            $member = Member::query()->create([
                'nim' => $member_activation->nim,
                'full_name' => $member_activation->full_name,
                'nickname' => $member_activation->nickname,
                'email' => $member_activation->email,
                'place_of_birth_code' => $member_activation->place_of_birth_code,
                'date_of_birth' => $member_activation->date_of_birth,
                'gender_id' => $member_activation->gender_id,
                'org_region_id' => $member_activation->org_region_id,
                'phone_number' => $member_activation->phone_number,
                'member_activation_id' => $member_activation->id,
                'is_created_from_member_activation' => true,
            ]);
        }

        // delete media supporting documents from member activation
        $member->media()
            ->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION)
            ->delete();

        // if has files supporting documents, attach to member
        $supportingDocuments = $member_activation->media()
            ->where('collection_name', Member::SUPPORTING_DOCUMENTS_COLLECTION)
            ->get();

        foreach ($supportingDocuments as $media) {
            $member->addMedia($media->getPath())
                ->toMediaCollection(Member::SUPPORTING_DOCUMENTS_COLLECTION);
        }

        // create member activation status log
        $member_activation->memberActivationStatusLogs()->create([
            'status_id' => MemberActivationStatus::VERIFIED->value,
            'notes' => $request->input('notes'),
        ]);

        // update or create kta
        $member->kta()->updateOrCreate([
            'member_id' => $member->id,
        ]);

        $kta = $member->kta;

        Notification::send(
            $member_activation,
            new MemberActivationAccepted([
                'email' => $member_activation->email,
                'full_name' => $member_activation->full_name,
                'kta_number' => $kta->number,
            ])
        );

        DB::commit();
        return redirect()
            ->route('admin.member-activations.index')
            ->with('success', 'Anggota berhasil diaktivasi.');
    }

    public function reject(MemberActivation $member_activation, Request $request)
    {
        $request->validate([
            'notes' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        $member_activation->memberActivationStatusLogs()->create([
            'status_id' => MemberActivationStatus::REJECTED->value,
            'notes' => $request->input('notes'),
        ]);

        Notification::send(
            $member_activation,
            new MemberActivationRejected([
                'id' => $member_activation->id,
                'email' => $member_activation->email,
                'full_name' => $member_activation->full_name,
                'notes' => $request->input('notes'),
            ])
        );

        DB::commit();
        return redirect()
            ->route('admin.member-activations.index')
            ->with('success', 'Anggota berhasil ditolak.');
    }
}
