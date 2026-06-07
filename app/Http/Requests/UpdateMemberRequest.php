<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Member;
use App\Models\MemberActivation;
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
            'place_of_birth_code' => $this->filled('place_of_birth_code') ? $this->input('place_of_birth_code') : null,
            'college_id' => $this->filled('college_id') ? $this->input('college_id') : null,
            'date_of_birth' => $this->filled('date_of_birth') ? $this->input('date_of_birth') : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $record = $this->memberOrActivationFromRoute();
        $table = $record instanceof MemberActivation ? 'member_activations' : 'members';

        return [
            'nim' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique($table, 'nim')->ignore($record->getKey()),
            ],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique($table, 'email')->ignore($record->getKey()),
            ],
            'place_of_birth_code' => ['nullable', 'string', 'size:4', 'exists:cities,code'],
            'date_of_birth' => ['nullable', 'date'],
            'gender_id' => ['nullable', Rule::enum(Gender::class)],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'college_id' => ['nullable', 'integer', 'exists:colleges,id'],
            'supporting_documents' => ['nullable', 'array', 'max:'.Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT],
            'supporting_documents.*' => Member::supportingDocumentItemRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $record = $this->memberOrActivationFromRoute();
            $existing = $record->getMedia(Member::SUPPORTING_DOCUMENTS_COLLECTION)->count();
            $uploadCount = $this->supportingDocumentsUploadCount();
            if ($existing + $uploadCount > Member::SUPPORTING_DOCUMENTS_MAX_TOTAL) {
                $v->errors()->add(
                    'supporting_documents',
                    'Jumlah dokumen pendukung tidak boleh lebih dari '.Member::SUPPORTING_DOCUMENTS_MAX_TOTAL.' (termasuk yang sudah diunggah).',
                );
            }
        });
    }

    /** Route resource admin.members memakai `{member}`; admin.member-activations memakai `{member_activation}`. */
    private function memberOrActivationFromRoute(): Member|MemberActivation
    {
        $member = $this->route('member');
        if ($member instanceof Member) {
            return $member;
        }

        $activation = $this->route('member_activation');
        if ($activation instanceof MemberActivation) {
            return $activation;
        }

        abort(500);
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
        return Arr::except($this->validator->validated(), ['supporting_documents']);
    }
}
