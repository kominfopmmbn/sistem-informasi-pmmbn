<?php

namespace App\Http\Requests;

use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = (int) floor(config('media-library.max_file_size') / 1024);

        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'about_heading' => ['nullable', 'string', 'max:255'],
            'about_content' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'goals' => ['nullable', 'array'],
            'goals.*' => ['nullable', 'string', 'max:1000'],
            'cover_photo' => ['nullable', 'image', 'max:'.$maxKb],
            'remove_cover' => ['sometimes', 'boolean'],
            'gallery' => ['nullable', 'array', 'max:'.Program::GALLERY_MAX_PER_SUBMIT],
            'gallery.*' => Program::galleryItemRules(),
        ];
    }
}
