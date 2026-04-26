<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('permission_ids')) {
            $this->merge(['permission_ids' => []]);
        }
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        $nameRule = [
            'required',
            'string',
            'max:255',
            Rule::unique('roles', 'name')
                ->where('guard_name', 'web')
                ->ignore($role->getKey()),
        ];

        return [
            'name' => $nameRule,
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'web'),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Role $role */
            $role = $this->route('role');
            if ($role->name === 'Administrator' && $this->input('name') !== 'Administrator') {
                $validator->errors()->add('name', 'Nama peran Administrator tidak dapat diubah.');
            }
        });
    }
}
