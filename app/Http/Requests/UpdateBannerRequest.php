<?php

namespace App\Http\Requests;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Slug isian admin dinormalisasi ("Daftar 2026" → "daftar-2026"); kosong → dibuat dari judul di controller. */
    protected function prepareForValidation(): void
    {
        $slug = Str::slug((string) $this->input('slug'));
        $this->merge(['slug' => $slug === '' ? null : $slug]);
    }

    public function rules(): array
    {
        $maxKb = (int) floor(config('media-library.max_file_size') / 1024);

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('banners', 'slug')->ignore($this->route('banner'))],
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:'.Banner::imageMimeList(), 'max:'.$maxKb],
        ];
    }
}
