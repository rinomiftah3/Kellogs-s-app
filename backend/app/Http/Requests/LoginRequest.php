<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     * to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules
     * that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [

            /*
            |------------------------------------------------------------------
            | Credentials
            |------------------------------------------------------------------
            */

            'email' => [

                'required',

                'string',

                'email',

                'max:255',
            ],

            'password' => [

                'required',

                'string',
            ],

            /*
            |------------------------------------------------------------------
            | Remember Login
            |------------------------------------------------------------------
            */

            'remember' => [

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

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.max' =>
                'Email maksimal 255 karakter.',

            'password.required' =>
                'Password wajib diisi.',

            'remember.boolean' =>
                'Remember me harus berupa true atau false.',
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

            'email' =>
                'email',

            'password' =>
                'password',

            'remember' =>
                'remember me',
        ];
    }
}