<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\Province;

class UpdateProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Province $province */
        $province = $this->route('province');

        return [
            'code' => [
                'required',
                'string',
                'size:2',
                'regex:/^[0-9]{2}$/',
                Rule::unique('provinces', 'code')->ignore($province->getKey()),
            ],
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
