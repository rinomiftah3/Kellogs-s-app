<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCustomerAddressRequest extends FormRequest
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

            'label' => filled($this->label)
                ? trim((string) $this->label)
                : null,

            'recipient_name' => filled($this->recipient_name)
                ? trim((string) $this->recipient_name)
                : null,

            'recipient_phone' => filled($this->recipient_phone)
                ? preg_replace(
                    '/\s+/',
                    '',
                    trim((string) $this->recipient_phone)
                )
                : null,

            'address' => filled($this->address)
                ? trim((string) $this->address)
                : null,

            'province' => filled($this->province)
                ? trim((string) $this->province)
                : null,

            'city' => filled($this->city)
                ? trim((string) $this->city)
                : null,

            'district' => filled($this->district)
                ? trim((string) $this->district)
                : null,

            'subdistrict' => filled($this->subdistrict)
                ? trim((string) $this->subdistrict)
                : null,

            'postal_code' => filled($this->postal_code)
                ? trim((string) $this->postal_code)
                : null,

            'notes' => filled($this->notes)
                ? trim((string) $this->notes)
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
        return [

            'customer_profile_id' => [

                'required',

                'integer',

                'exists:customer_profiles,id',
            ],

            'label' => [

                'required',

                'string',

                'max:100',
            ],

            'recipient_name' => [

                'required',

                'string',

                'max:255',
            ],

            'recipient_phone' => [

                'required',

                'string',

                'max:30',

                'regex:/^[0-9+\-()]+$/',
            ],

            'address' => [

                'required',

                'string',
            ],

            'province' => [

                'required',

                'string',

                'max:255',
            ],

            'city' => [

                'required',

                'string',

                'max:255',
            ],

            'district' => [

                'required',

                'string',

                'max:255',
            ],

            'subdistrict' => [

                'required',

                'string',

                'max:255',
            ],

            'postal_code' => [

                'required',

                'string',

                'max:20',

                'regex:/^[0-9A-Za-z\- ]+$/',
            ],

            'latitude' => [

                'nullable',

                'numeric',

                'between:-90,90',
            ],

            'longitude' => [

                'nullable',

                'numeric',

                'between:-180,180',
            ],

            'is_default' => [

                'sometimes',

                'boolean',
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],

            'notes' => [

                'nullable',

                'string',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'customer_profile_id.required' =>
                'Pelanggan wajib dipilih.',

            'customer_profile_id.exists' =>
                'Pelanggan tidak ditemukan.',

            'label.required' =>
                'Label alamat wajib diisi.',

            'label.max' =>
                'Label alamat maksimal 100 karakter.',

            'recipient_name.required' =>
                'Nama penerima wajib diisi.',

            'recipient_phone.required' =>
                'Nomor telepon penerima wajib diisi.',

            'recipient_phone.regex' =>
                'Format nomor telepon penerima tidak valid.',

            'address.required' =>
                'Alamat wajib diisi.',

            'province.required' =>
                'Provinsi wajib diisi.',

            'city.required' =>
                'Kota/Kabupaten wajib diisi.',

            'district.required' =>
                'Kecamatan wajib diisi.',

            'subdistrict.required' =>
                'Kelurahan/Desa wajib diisi.',

            'postal_code.required' =>
                'Kode pos wajib diisi.',

            'postal_code.regex' =>
                'Format kode pos tidak valid.',

            'latitude.between' =>
                'Latitude harus berada di antara -90 sampai 90.',

            'longitude.between' =>
                'Longitude harus berada di antara -180 sampai 180.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'customer_profile_id' =>
                'pelanggan',

            'label' =>
                'label alamat',

            'recipient_name' =>
                'nama penerima',

            'recipient_phone' =>
                'nomor telepon penerima',

            'address' =>
                'alamat',

            'province' =>
                'provinsi',

            'city' =>
                'kota/kabupaten',

            'district' =>
                'kecamatan',

            'subdistrict' =>
                'kelurahan/desa',

            'postal_code' =>
                'kode pos',

            'latitude' =>
                'latitude',

            'longitude' =>
                'longitude',

            'is_default' =>
                'alamat utama',

            'is_active' =>
                'status alamat',

            'notes' =>
                'catatan',
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
                | Latitude dan longitude harus berpasangan
                |--------------------------------------------------------------------------
                */

                if (

                    filled($this->latitude)
                    xor
                    filled($this->longitude)

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'latitude',
                            'Latitude dan longitude harus diisi bersamaan.'
                        );
                }
            }
        );
    }
}