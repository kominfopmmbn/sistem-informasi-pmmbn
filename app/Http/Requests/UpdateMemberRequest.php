<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Member;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nim' => $this->filled('nim') ? $this->input('nim') : null,
            'email' => $this->filled('email') ? $this->input('email') : null,
            'gender_id' => $this->filled('gender_id') ? $this->input('gender_id') : null,
            'org_region_id' => $this->filled('org_region_id') ? $this->input('org_region_id') : null,
            'place_of_birth_code' => $this->filled('place_of_birth_code') ? $this->input('place_of_birth_code') : null,
            'province_code' => $this->filled('province_code') ? $this->input('province_code') : null,
            'date_of_birth' => $this->filled('date_of_birth') ? $this->input('date_of_birth') : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Member $member */
        $member = $this->route('member');
        $provinceCode = $this->input('province_code');

        return [
            'nim' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('members', 'nim')->ignore($member->getKey()),
            ],
            'full_name' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique('members', 'email')->ignore($member->getKey()),
            ],
            'province_code' => ['nullable', 'string', 'size:2', 'exists:provinces,code', 'required_with:place_of_birth_code'],
            'place_of_birth_code' => [
                'nullable',
                'string',
                'size:4',
                'required_with:province_code',
                filled($provinceCode)
                    ? Rule::exists('cities', 'code')->where('province_code', (string) $provinceCode)
                    : Rule::exists('cities', 'code')->where(static fn ($query) => $query->whereRaw('1 = 0')),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'gender_id' => ['nullable', Rule::enum(Gender::class)],
            'org_region_id' => ['nullable', 'integer', 'exists:org_regions,id'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'supporting_documents' => ['nullable', 'array', 'max:'.Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT],
            'supporting_documents.*' => Member::supportingDocumentItemRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Member $member */
            $member = $this->route('member');
            $existing = $member->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION)->count();
            $uploadCount = $this->supportingDocumentsUploadCount();
            if ($existing + $uploadCount > Member::SUPPORTING_DOCUMENTS_MAX_TOTAL) {
                $v->errors()->add(
                    'supporting_documents',
                    'Jumlah dokumen pendukung tidak boleh lebih dari '.Member::SUPPORTING_DOCUMENTS_MAX_TOTAL.' (termasuk yang sudah diunggah).',
                );
            }
        });
    }

    private function supportingDocumentsUploadCount(): int
    {
        $files = $this->file('supporting_documents');
        if ($files === null) {
            return 0;
        }

        $files = is_array($files) ? $files : [$files];

        return count(array_filter(
            $files,
            static fn ($f) => $f instanceof UploadedFile,
        ));
    }

    public function validatedPersistable(): array
    {
        return Arr::except($this->validator->validated(), ['province_code', 'supporting_documents']);
    }
}
