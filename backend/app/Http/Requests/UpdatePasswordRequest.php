<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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

            'current_password' => $this->has('current_password')
                ? trim(
                    (string) $this->input(
                        'current_password'
                    )
                )
                : null,

            'password' => $this->has('password')
                ? trim(
                    (string) $this->input(
                        'password'
                    )
                )
                : null,

            'password_confirmation' => $this->has(
                'password_confirmation'
            )
                ? trim(
                    (string) $this->input(
                        'password_confirmation'
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
            | Current Password
            |------------------------------------------------------------------
            */

            'current_password' => [

                'required',

                'string',

                'current_password',
            ],

            /*
            |------------------------------------------------------------------
            | New Password
            |------------------------------------------------------------------
            */

            'password' => [

                'required',

                'confirmed',

                Password::defaults(),
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

            'current_password.required' =>
                'Password saat ini wajib diisi.',

            'current_password.current_password' =>
                'Password saat ini tidak sesuai.',

            'password.required' =>
                'Password baru wajib diisi.',

            'password.confirmed' =>
                'Konfirmasi password baru tidak sesuai.',
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

            'current_password' =>
                'password saat ini',

            'password' =>
                'password baru',

            'password_confirmation' =>
                'konfirmasi password baru',
        ];
    }
}