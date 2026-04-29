<?php

namespace App\Http\Requests;

use App\Models\College;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\Province;

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
        $provinceCode = $this->resolveProvinceCode();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colleges', 'name')->ignore($college->getKey()),
            ],
            'province_id' => ['required', 'integer', 'exists:indonesia_provinces,id'],
            'city_id' => [
                'required',
                'integer',
                $provinceCode !== null
                    ? Rule::exists('indonesia_cities', 'id')->where('province_code', $provinceCode)
                    : Rule::exists('indonesia_cities', 'id')->where(static fn ($query) => $query->whereRaw('1 = 0')),
            ],
        ];
    }

    private function resolveProvinceCode(): ?string
    {
        $id = $this->input('province_id');
        if ($id === null || $id === '') {
            return null;
        }

        return Province::query()->whereKey($id)->value('code');
    }
}
