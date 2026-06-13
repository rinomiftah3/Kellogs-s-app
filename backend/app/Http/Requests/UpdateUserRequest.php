<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        return $this->user()?->can('users.update')
            ?? false;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' => $this->has('name')
                ? trim((string) $this->input('name'))
                : null,

            'email' => $this->has('email')
                ? strtolower(
                    trim(
                        (string) $this->input('email')
                    )
                )
                : null,

            'role' => $this->has('role')
                ? trim(
                    (string) $this->input('role')
                )
                : null,

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input('is_active'),
        ]);
    }

    /**
     * Validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [

            /*
            |------------------------------------------------------------------
            | User Information
            |------------------------------------------------------------------
            */

            'name' => [

                'required',

                'string',

                'min:3',

                'max:255',
            ],

            'email' => [

                'required',

                'string',

                'email',

                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $user?->id
                ),
            ],

            /*
            |------------------------------------------------------------------
            | Password
            |------------------------------------------------------------------
            */

            'password' => [

                'nullable',

                'confirmed',

                Password::defaults(),
            ],

            /*
            |------------------------------------------------------------------
            | Role Assignment
            |------------------------------------------------------------------
            */

            'role' => [

                'required',

                'string',

                Rule::exists(
                    'roles',
                    'name'
                ),
            ],

            /*
            |------------------------------------------------------------------
            | Avatar
            |------------------------------------------------------------------
            */

            'avatar' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',
            ],

            /*
            |------------------------------------------------------------------
            | Account Status
            |------------------------------------------------------------------
            */

            'is_active' => [

                'sometimes',

                'boolean',
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
                'Nama wajib diisi.',

            'name.min' =>
                'Nama minimal 3 karakter.',

            'name.max' =>
                'Nama maksimal 255 karakter.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.max' =>
                'Email maksimal 255 karakter.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'role.required' =>
                'Role wajib dipilih.',

            'role.exists' =>
                'Role yang dipilih tidak valid.',

            'avatar.image' =>
                'Avatar harus berupa gambar.',

            'avatar.mimes' =>
                'Avatar harus berformat JPG, JPEG, PNG, atau WEBP.',

            'avatar.max' =>
                'Ukuran avatar maksimal 2 MB.',

            'is_active.boolean' =>
                'Status aktif harus berupa true atau false.',
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
                'nama',

            'email' =>
                'email',

            'password' =>
                'password',

            'password_confirmation' =>
                'konfirmasi password',

            'role' =>
                'role',

            'avatar' =>
                'avatar',

            'is_active' =>
                'status aktif',
        ];
    }
}