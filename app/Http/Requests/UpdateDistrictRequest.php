<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\District;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var District $district */
        $district = $this->route('district');

        return [
            'code' => [
                'required',
                'string',
                'size:7',
                'regex:/^[0-9]{7}$/',
                Rule::unique('districts', 'code')->ignore($district->getKey()),
            ],
            'city_code' => ['required', 'string', 'size:4', 'exists:cities,code'],
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
