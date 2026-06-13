<?php

namespace App\Http\Requests;

use App\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreVoucherRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()?->can('manage vouchers')
            ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' => filled($this->name)
                ? trim((string) $this->name)
                : null,

            'code' => filled($this->code)
                ? strtoupper(trim((string) $this->code))
                : null,

            'description' => filled($this->description)
                ? trim((string) $this->description)
                : null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                'unique:vouchers,code',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:65535',
            ],

            'type' => [
                'required',
                'string',
                Rule::in([
                    Voucher::TYPE_FIXED,
                    Voucher::TYPE_PERCENTAGE,
                    Voucher::TYPE_FREE_SHIPPING,
                ]),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'maximum_discount' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'minimum_purchase' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'usage_limit' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'usage_per_user' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'is_public' => [
                'sometimes',
                'boolean',
            ],

            'is_stackable' => [
                'sometimes',
                'boolean',
            ],

            'start_at' => [
                'required',
                'date',
            ],

            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'code.unique' =>
                'Kode voucher sudah digunakan.',

            'code.alpha_dash' =>
                'Kode voucher hanya boleh berisi huruf, angka, dash, dan underscore.',

            'type.in' =>
                'Tipe voucher tidak valid.',

            'end_at.after' =>
                'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Friendly Attributes
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [

            'name' => 'nama voucher',
            'code' => 'kode voucher',
            'description' => 'deskripsi',
            'type' => 'tipe voucher',
            'discount_value' => 'nilai diskon',
            'maximum_discount' => 'maksimum diskon',
            'minimum_purchase' => 'minimum pembelian',
            'usage_limit' => 'batas penggunaan',
            'usage_per_user' => 'batas penggunaan per pengguna',
            'is_active' => 'status aktif',
            'is_public' => 'visibilitas publik',
            'is_stackable' => 'status stackable',
            'start_at' => 'tanggal mulai',
            'end_at' => 'tanggal berakhir',
            'metadata' => 'metadata',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Validation
    |--------------------------------------------------------------------------
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
                | Percentage Voucher Rules
                |--------------------------------------------------------------------------
                */

                if (
                    $this->type === Voucher::TYPE_PERCENTAGE
                ) {

                    if (
                        (float) $this->discount_value > 100
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Diskon persentase tidak boleh lebih dari 100%.'
                            );
                    }

                    if (
                        ! $this->filled('maximum_discount')
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'maximum_discount',
                                'Maksimum diskon wajib diisi untuk voucher persentase.'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Fixed Voucher Rules
                |--------------------------------------------------------------------------
                */

                if (
                    $this->type === Voucher::TYPE_FIXED
                    &&
                    $this->filled('maximum_discount')
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'maximum_discount',
                            'Voucher fixed tidak boleh memiliki maksimum diskon.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Free Shipping Rules
                |--------------------------------------------------------------------------
                */

                if (
                    $this->type === Voucher::TYPE_FREE_SHIPPING
                ) {

                    if (
                        (float) $this->discount_value !== 0.0
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Voucher gratis ongkir harus memiliki nilai diskon 0.'
                            );
                    }

                    if (
                        $this->filled('maximum_discount')
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'maximum_discount',
                                'Voucher gratis ongkir tidak boleh memiliki maksimum diskon.'
                            );
                    }
                }
            }
        );
    }
}