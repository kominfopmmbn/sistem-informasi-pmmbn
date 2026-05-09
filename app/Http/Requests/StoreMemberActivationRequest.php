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
        $provinceCode = $this->input('province_code');

        return [
            'nim' => ['required', 'string', 'max:255', 'unique:member_activations,nim'],
            'full_name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
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
            'province_code' => ['required', 'string', 'size:2', 'exists:provinces,code', 'required_with:place_of_birth_code'],
            'place_of_birth_code' => [
                'required',
                'string',
                'size:4',
                'required_with:province_code',
                filled($provinceCode)
                    ? Rule::exists('cities', 'code')->where('province_code', (string) $provinceCode)
                    : Rule::exists('cities', 'code')->where(static fn ($query) => $query->whereRaw('1 = 0')),
            ],
            'date_of_birth' => ['required', 'date'],
            'gender_id' => ['required', Rule::enum(Gender::class)],
            'org_region_id' => ['nullable', 'integer', 'exists:org_regions,id'],
            'phone_number' => ['required', 'string', 'max:255'],
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

    /** Province hanya untuk validasi kota tempat lahir; tidak disimpan. */
    public function validatedPersistable(): array
    {
        return Arr::except($this->validator->validated(), ['province_code', 'supporting_documents']);
    }
}
