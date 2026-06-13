<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     * to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('users.create')
            ?? false;
    }

    /**
     * Get the validation rules
     * that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [

            /*
            |------------------------------------------------------------------
            | User Information
            |------------------------------------------------------------------
            */

            'name' => [

                'required',

                'string',

                'max:255',
            ],

            'email' => [

                'required',

                'string',

                'email',

                'max:255',

                'unique:users,email',
            ],

            /*
            |------------------------------------------------------------------
            | Authentication
            |------------------------------------------------------------------
            */

            'password' => [

                'required',

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

                'exists:roles,name',
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

            'password.required' =>
                'Password wajib diisi.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'role.required' =>
                'Role wajib dipilih.',

            'role.exists' =>
                'Role yang dipilih tidak valid.',

            'is_active.boolean' =>
                'Status aktif harus berupa true atau false.',

            'avatar.image' =>
                'Avatar harus berupa gambar.',

            'avatar.mimes' =>
                'Avatar harus berformat JPG, JPEG, PNG, atau WEBP.',

            'avatar.max' =>
                'Ukuran avatar maksimal 2 MB.',
        ];
    }

    /**
     * Custom attribute names.
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

            'is_active' =>
                'status aktif',

            'avatar' =>
                'avatar',
        ];
    }
}