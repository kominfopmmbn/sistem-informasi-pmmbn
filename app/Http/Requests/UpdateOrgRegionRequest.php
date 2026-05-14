<?php

namespace App\Http\Requests;

use App\Models\OrgRegion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrgRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var OrgRegion $orgRegion */
        $orgRegion = $this->route('org_region');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('org_regions', 'name')->ignore($orgRegion->getKey()),
            ],
            'code' => [
                'required',
                'string',
                // 'max:2',
                'digits:2',
                Rule::unique('org_regions', 'code')->ignore($orgRegion->getKey()),
            ],
        ];
    }
}
