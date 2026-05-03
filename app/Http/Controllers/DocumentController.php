<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = isset($filters['q']) ? trim($filters['q']) : '';

        $query = Document::query()
            ->with(['media' => fn ($mq) => $mq->where('collection_name', Document::FILE_COLLECTION)])
            ->latest('updated_at');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('title', 'like', $like);
        }

        $documents = $query->paginate(15)->withQueryString();

        $filterState = ['q' => $q];

        return view('admin.documents.index', compact('documents', 'filterState'));
    }

    public function create(): View
    {
        return view('admin.documents.create');
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $document = Document::create($request->safe()->only('title'));
        $document->addMediaFromRequest('file')->toMediaCollection(Document::FILE_COLLECTION);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document): View
    {
        return view('admin.documents.edit', compact('document'));
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update($request->safe()->only('title'));

        if ($request->hasFile('file')) {
            $document->addMediaFromRequest('file')->toMediaCollection(Document::FILE_COLLECTION);
        }

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * @param Model $document
     * @return RedirectResponse
     */
    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
