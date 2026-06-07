<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Member;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nim' => $this->filled('nim') ? $this->input('nim') : null,
            'email' => $this->filled('email') ? $this->input('email') : null,
            'gender_id' => $this->filled('gender_id') ? $this->input('gender_id') : null,
            'place_of_birth_code' => $this->filled('place_of_birth_code') ? $this->input('place_of_birth_code') : null,
            'village_code' => $this->filled('village_code') ? $this->input('village_code') : null,
            'college_id' => $this->filled('college_id') ? $this->input('college_id') : null,
            'regional_leader_id' => $this->filled('regional_leader_id') ? $this->input('regional_leader_id') : null,
            'date_of_birth' => $this->filled('date_of_birth') ? $this->input('date_of_birth') : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nim' => ['nullable', 'string', 'max:255', 'unique:members,nim'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255', 'unique:members,email'],
            'place_of_birth_code' => ['nullable', 'string', 'size:4', 'exists:cities,code'],
            'date_of_birth' => ['nullable', 'date'],
            'gender_id' => ['nullable', Rule::enum(Gender::class)],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'village_code' => ['nullable', 'string', 'size:10', 'exists:villages,code'],
            'college_id' => ['nullable', 'integer', 'exists:colleges,id'],
            'regional_leader_id' => ['nullable', 'integer', 'exists:regional_leaders,id'],
            'supporting_documents' => ['nullable', 'array', 'max:'.Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT],
            'supporting_documents.*' => Member::supportingDocumentItemRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $uploadCount = $this->supportingDocumentsUploadCount();
            if ($uploadCount > Member::SUPPORTING_DOCUMENTS_MAX_TOTAL) {
                $v->errors()->add(
                    'supporting_documents',
                    'Jumlah dokumen pendukung tidak boleh lebih dari '.Member::SUPPORTING_DOCUMENTS_MAX_TOTAL.'.',
                );
            }
        });
    }

    /** Hitung slot file pada input multi-upload (abaikan entri kosong). */
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

    /** `supporting_documents` ditangani terpisah lewat MediaLibrary; tidak ikut disimpan ke kolom. */
    public function validatedPersistable(): array
    {
        return Arr::except($this->validator->validated(), ['supporting_documents']);
    }
}
