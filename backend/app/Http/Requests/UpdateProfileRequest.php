<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        return $this->user() !== null;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' => $this->has('name')
                ? trim(
                    (string) $this->input('name')
                )
                : null,

            'email' => $this->has('email')
                ? strtolower(
                    trim(
                        (string) $this->input('email')
                    )
                )
                : null,
        ]);
    }

    /**
     * Get validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [

            /*
            |------------------------------------------------------------------
            | Profile Information
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
                    $this->user()?->id
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

            'avatar.image' =>
                'Avatar harus berupa gambar.',

            'avatar.mimes' =>
                'Avatar harus berformat JPG, JPEG, PNG, atau WEBP.',

            'avatar.max' =>
                'Ukuran avatar maksimal 2 MB.',
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

            'avatar' =>
                'avatar',
        ];
    }
}