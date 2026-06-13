<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine whether the user is authorized.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('roles.update')
            ?? false;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $permissions = $this->input(
            'permissions',
            []
        );

        $permissions = is_array($permissions)
            ? array_values(
                array_unique(
                    array_map(
                        fn ($permission) => trim(
                            (string) $permission
                        ),
                        $permissions
                    )
                )
            )
            : [];

        $this->merge([

            'name' => $this->has('name')
                ? trim(
                    (string) $this->input('name')
                )
                : null,

            'guard_name' => $this->has('guard_name')
                ? trim(
                    (string) $this->input(
                        'guard_name'
                    )
                )
                : 'web',

            'permissions' => $permissions,
        ]);
    }

    /**
     * Get validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $role = $this->route('role');

        $roleId = $role instanceof Role
            ? $role->id
            : $role;

        return [

            /*
            |------------------------------------------------------------------
            | Role Information
            |------------------------------------------------------------------
            */

            'name' => [

                'required',

                'string',

                'max:255',

                Rule::unique(
                    'roles',
                    'name'
                )->ignore(
                    $roleId
                ),
            ],

            'guard_name' => [

                'required',

                'string',

                'max:255',
            ],

            /*
            |------------------------------------------------------------------
            | Permissions
            |------------------------------------------------------------------
            */

            'permissions' => [

                'nullable',

                'array',
            ],

            'permissions.*' => [

                'string',

                Rule::exists(
                    'permissions',
                    'name'
                ),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [

            'name.required' =>
                'Nama role wajib diisi.',

            'name.max' =>
                'Nama role maksimal 255 karakter.',

            'name.unique' =>
                'Nama role sudah digunakan.',

            'guard_name.required' =>
                'Guard name wajib diisi.',

            'guard_name.max' =>
                'Guard name maksimal 255 karakter.',

            'permissions.array' =>
                'Permissions harus berupa array.',

            'permissions.*.exists' =>
                'Permission yang dipilih tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [

            'name' =>
                'nama role',

            'guard_name' =>
                'guard',

            'permissions' =>
                'permissions',

            'permissions.*' =>
                'permission',
        ];
    }
}