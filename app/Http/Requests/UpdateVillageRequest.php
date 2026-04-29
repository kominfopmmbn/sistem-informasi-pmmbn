<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\Village;

class UpdateVillageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Village $village */
        $village = $this->route('village');

        return [
            'code' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                Rule::unique('villages', 'code')->ignore($village->getKey()),
            ],
            'district_code' => ['required', 'string', 'size:7', 'exists:districts,code'],
            'name' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'json'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $meta = $this->input('meta');
        if ($meta === '') {
            $this->merge(['meta' => null]);
        }
    }
}
