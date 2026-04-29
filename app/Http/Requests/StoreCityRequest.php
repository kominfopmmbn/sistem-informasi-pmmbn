<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/', 'unique:cities,code'],
            'province_code' => ['required', 'string', 'size:2', 'exists:provinces,code'],
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
