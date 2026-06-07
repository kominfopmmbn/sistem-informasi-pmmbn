<?php

namespace App\Http\Requests;

use App\Models\RegionalLeader;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionalLeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var RegionalLeader $regionalLeader */
        $regionalLeader = $this->route('regional_leader');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('regional_leaders', 'code')->ignore($regionalLeader->getKey()),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
