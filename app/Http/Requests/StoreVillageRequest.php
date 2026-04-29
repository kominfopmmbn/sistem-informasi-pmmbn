<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVillageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/', 'unique:villages,code'],
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
