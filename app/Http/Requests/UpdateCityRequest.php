<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\City;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var City $city */
        $city = $this->route('city');

        return [
            'code' => [
                'required',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
                Rule::unique('cities', 'code')->ignore($city->getKey()),
            ],
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
