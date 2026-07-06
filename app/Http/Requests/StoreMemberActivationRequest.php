<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Member;
use App\Models\MemberActivation;
use App\Models\MemberActivationEmailOtpVerification;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreMemberActivationRequest extends FormRequest
{
    /** True bila pendaftar memilih opsi "Lainnya" pada perguruan tinggi. */
    private bool $choseOther = false;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->choseOther = $this->input('college_id') === 'other';

        if ($this->choseOther) {
            // "Lainnya": tidak ada perguruan tinggi terdaftar; nama diisi di college_other.
            $this->merge(['college_id' => null]);
        } else {
            // Perguruan tinggi terdaftar dipilih (atau kosong): abaikan teks bebas.
            $this->merge(['college_other' => null]);
        }
    }

    public function rules(): array
    {
        // Identitas pendaftaran adalah email (store memakai updateOrCreate berbasis email),
        // jadi baris milik email ini diabaikan agar pendaftar boleh submit ulang/memperbarui datanya.
        $existingActivationId = MemberActivation::query()
            ->where('email', (string) $this->input('email'))
            ->value('id');

        return [
            'nim' => [
                'required',
                'string',
                'max:255',
                Rule::unique('member_activations', 'nim')->ignore($existingActivationId),
                Rule::unique('members', 'nim'),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('member_activations', 'email')->ignore($existingActivationId),
                Rule::unique('members', 'email'),
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
            'village_code' => ['required', 'string', 'size:10', 'exists:villages,code'],
            'college_id' => $this->choseOther
                ? ['nullable']
                : ['required', 'integer', 'exists:colleges,id'],
            'college_other' => $this->choseOther
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'],
            'supporting_documents' => ['nullable', 'array', 'max:'.Member::SUPPORTING_DOCUMENTS_MAX_PER_SUBMIT],
            'supporting_documents.*' => Member::supportingDocumentItemRules(),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'college_id' => 'Perguruan tinggi',
            'college_other' => 'Nama perguruan tinggi',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nim.unique' => 'NIM sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
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
