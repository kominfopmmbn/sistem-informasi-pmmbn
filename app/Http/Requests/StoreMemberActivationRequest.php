<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Member;
use App\Models\MemberActivationEmailOtpVerification;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreMemberActivationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                function ($attribute, $value, $fail) {
                    $memberActivationEmailOtpVerification = MemberActivationEmailOtpVerification::query()
                        ->where('email', $value)
                        ->whereNotNull('verified_at')
                        ->first();
                    if (! $memberActivationEmailOtpVerification) {
                        $fail('Email belum diverifikasi. Silakan lakukan verifikasi email terlebih dahulu.');
                    }
                },
            ],
            'place_of_birth_code' => ['required', 'string', 'size:4', 'exists:cities,code'],
            'date_of_birth' => ['required', 'date'],
            'gender_id' => ['required', Rule::enum(Gender::class)],
            'phone_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'college_id' => ['required', 'integer', 'exists:colleges,id'],
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
