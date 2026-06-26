<?php

namespace App\Http\Requests;

use App\Rules\QuillContentNotEmpty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');

        return $article === null
            ? false
            : $this->user()->can('update', $article);
    }

    /** Aksi terbit hanya berlaku jika user memang punya izin publish. */
    public function isPublishing(): bool
    {
        return $this->string('save_action')->toString() === 'publish'
            && $this->user()->can('articles.publish');
    }

    public function rules(): array
    {
        $isPublish = $this->isPublishing();

        return [
            'save_action' => ['required', Rule::in(['draft', 'publish'])],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => $isPublish
                ? ['required', 'string', new QuillContentNotEmpty]
                : ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'published_at' => $isPublish
                ? ['required', 'date']
                : ['nullable', 'date'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
            'remove_cover' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $publishedAt = $this->input('published_at');
        if ($publishedAt === null || $publishedAt === '' || (is_string($publishedAt) && trim($publishedAt) === '')) {
            $this->merge(['published_at' => null]);
        }
    }
}
