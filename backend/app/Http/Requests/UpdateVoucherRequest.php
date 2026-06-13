<?php

namespace App\Http\Requests;

use App\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVoucherRequest extends FormRequest
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
                : $this->name,

            'code' => filled($this->code)
                ? strtoupper(trim((string) $this->code))
                : $this->code,

            'description' => filled($this->description)
                ? trim((string) $this->description)
                : $this->description,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        $routeVoucher = $this->route('voucher');

        $voucherId = $routeVoucher instanceof Voucher
            ? $routeVoucher->getKey()
            : $this->route('id');

        return [

            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'code' => [
                'sometimes',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('vouchers', 'code')
                    ->ignore($voucherId),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:65535',
            ],

            'type' => [
                'sometimes',
                'string',
                Rule::in([
                    Voucher::TYPE_FIXED,
                    Voucher::TYPE_PERCENTAGE,
                    Voucher::TYPE_FREE_SHIPPING,
                ]),
            ],

            'discount_value' => [
                'sometimes',
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
                'sometimes',
                'nullable',
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
                'sometimes',
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
                'sometimes',
                'date',
            ],

            'end_at' => [
                'sometimes',
                'date',
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

                /** @var Voucher|null $voucher */
                $voucher = $this->route('voucher');

                /*
                |--------------------------------------------------------------------------
                | Determine Effective Values
                |--------------------------------------------------------------------------
                */

                $type = $this->input(
                    'type',
                    $voucher?->type
                );

                $discountValue = $this->input(
                    'discount_value',
                    $voucher?->discount_value
                );

                $maximumDiscount = $this->input(
                    'maximum_discount',
                    $voucher?->maximum_discount
                );

                $startAt = $this->input(
                    'start_at',
                    $voucher?->start_at
                );

                $endAt = $this->input(
                    'end_at',
                    $voucher?->end_at
                );

                /*
                |--------------------------------------------------------------------------
                | Date Validation
                |--------------------------------------------------------------------------
                */

                if (
                    $startAt !== null
                    &&
                    $endAt !== null
                    &&
                    strtotime((string) $endAt)
                    <= strtotime((string) $startAt)
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'end_at',
                            'Tanggal berakhir harus setelah tanggal mulai.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Percentage Voucher
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Voucher::TYPE_PERCENTAGE
                ) {

                    if (
                        (float) $discountValue > 100
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Diskon persentase tidak boleh lebih dari 100%.'
                            );
                    }

                    if (
                        $maximumDiscount === null
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
                | Fixed Voucher
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Voucher::TYPE_FIXED
                    &&
                    $maximumDiscount !== null
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
                | Free Shipping Voucher
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Voucher::TYPE_FREE_SHIPPING
                ) {

                    if (
                        (float) $discountValue !== 0.0
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Voucher gratis ongkir harus memiliki nilai diskon 0.'
                            );
                    }

                    if (
                        $maximumDiscount !== null
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