<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:2', 'regex:/^[0-9]{2}$/', 'unique:provinces,code'],
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
