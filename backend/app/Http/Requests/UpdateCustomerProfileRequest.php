<?php

namespace App\Http\Requests;

use App\Models\CustomerProfile;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCustomerProfileRequest extends FormRequest
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

            'full_name' => filled($this->full_name)
                ? trim((string) $this->full_name)
                : null,

            'phone' => filled($this->phone)
                ? preg_replace(
                    '/\s+/',
                    '',
                    trim((string) $this->phone)
                )
                : null,

            'bio' => filled($this->bio)
                ? trim((string) $this->bio)
                : null,

            'membership_level' => filled(
                $this->membership_level
            )
                ? strtolower(
                    trim(
                        (string) $this->membership_level
                    )
                )
                : null,
        ]);
    }

    /**
     * Get validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $profile = $this->route('customer_profile')
            ?? $this->route('customerProfile');

        $profileId = $profile instanceof CustomerProfile
            ? $profile->id
            : $profile;

        return [

            'user_id' => [

                'nullable',

                'integer',

                'exists:users,id',

                Rule::unique(
                    'customer_profiles',
                    'user_id'
                )->ignore($profileId),
            ],

            'full_name' => [

                'required',

                'string',

                'max:255',
            ],

            'phone' => [

                'nullable',

                'string',

                'max:30',

                'regex:/^[0-9+\-()]+$/',

                Rule::unique(
                    'customer_profiles',
                    'phone'
                )->ignore($profileId),
            ],

            'gender' => [

                'nullable',

                'string',

                Rule::in([

                    CustomerProfile::GENDER_MALE,

                    CustomerProfile::GENDER_FEMALE,
                ]),
            ],

            'birth_date' => [

                'nullable',

                'date',

                'before:today',
            ],

            'bio' => [

                'nullable',

                'string',
            ],

            'membership_level' => [

                'nullable',

                'string',

                Rule::in([

                    CustomerProfile::LEVEL_REGULAR,

                    CustomerProfile::LEVEL_SILVER,

                    CustomerProfile::LEVEL_GOLD,

                    CustomerProfile::LEVEL_PLATINUM,
                ]),
            ],

            'email_subscribed' => [

                'sometimes',

                'boolean',
            ],

            'sms_subscribed' => [

                'sometimes',

                'boolean',
            ],

            'push_subscribed' => [

                'sometimes',

                'boolean',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'user_id.exists' =>
                'User tidak ditemukan.',

            'user_id.unique' =>
                'User sudah memiliki profil pelanggan.',

            'full_name.required' =>
                'Nama lengkap wajib diisi.',

            'full_name.max' =>
                'Nama lengkap maksimal 255 karakter.',

            'phone.unique' =>
                'Nomor telepon sudah digunakan.',

            'phone.regex' =>
                'Format nomor telepon tidak valid.',

            'gender.in' =>
                'Gender tidak valid.',

            'birth_date.before' =>
                'Tanggal lahir harus sebelum hari ini.',

            'membership_level.in' =>
                'Level membership tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'user_id' =>
                'user',

            'full_name' =>
                'nama lengkap',

            'phone' =>
                'nomor telepon',

            'gender' =>
                'gender',

            'birth_date' =>
                'tanggal lahir',

            'bio' =>
                'bio',

            'membership_level' =>
                'level membership',

            'email_subscribed' =>
                'langganan email',

            'sms_subscribed' =>
                'langganan SMS',

            'push_subscribed' =>
                'langganan push notification',
        ];
    }

    /**
     * Additional validation.
     */
    public function withValidator(
        Validator $validator
    ): void {

        $validator->after(

            function (
                Validator $validator
            ) {

                /*
                |--------------------------------------------------------------------------
                | Minimal usia pelanggan 13 tahun
                |--------------------------------------------------------------------------
                */

                if (

                    filled($this->birth_date)

                    &&

                    now()
                        ->diffInYears(
                            $this->birth_date
                        ) < 13
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'birth_date',
                            'Usia pelanggan minimal 13 tahun.'
                        );
                }
            }
        );
    }
}