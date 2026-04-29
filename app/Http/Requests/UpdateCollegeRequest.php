<?php

namespace App\Http\Requests;

use App\Models\College;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollegeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var College $college */
        $college = $this->route('college');
        $provinceCode = $this->input('province_code');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colleges', 'name')->ignore($college->getKey()),
            ],
            'province_code' => ['required', 'string', 'size:2', 'exists:provinces,code'],
            'city_code' => [
                'required',
                'string',
                'size:4',
                filled($provinceCode)
                    ? Rule::exists('cities', 'code')->where('province_code', (string) $provinceCode)
                    : Rule::exists('cities', 'code')->where(static fn ($query) => $query->whereRaw('1 = 0')),
            ],
        ];
    }
}
