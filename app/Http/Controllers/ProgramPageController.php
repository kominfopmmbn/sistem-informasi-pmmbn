<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\View\View;

class ProgramPageController extends Controller
{
    public function index(): View
    {
        $programs = Program::active()
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('front.program-unggulan.index', compact('programs'));
    }

    public function show(string $slug): View
    {
        $program = Program::active()
            ->where('slug', $slug)
            ->with(['goals', 'media'])
            ->firstOrFail();

        $otherPrograms = Program::active()
            ->where('id', '!=', $program->id)
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(8)
            ->get();

        return view('front.program-unggulan.show', compact('program', 'otherPrograms'));
    }
}
