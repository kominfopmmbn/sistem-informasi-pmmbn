<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\View\View;

class DownloadPageController extends Controller
{
    public function index(): View
    {
        $documents = Document::query()
            ->whereHas('media', fn ($q) => $q->where('collection_name', Document::FILE_COLLECTION))
            ->with(['media' => fn ($mq) => $mq->where('collection_name', Document::FILE_COLLECTION)])
            ->latest('updated_at')
            ->get();

        return view('front.download.index', compact('documents'));
    }
}
