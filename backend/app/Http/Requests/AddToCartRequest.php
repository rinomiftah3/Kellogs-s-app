<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * AddToCartRequest
 *
 * Validates adding a product SKU to the authenticated customer's cart.
 *
 * Laravel 13
 * PHP 8.4
 */
class AddToCartRequest extends FormRequest
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
        return $this->user()?->hasRole('customer') ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'notes' => filled($this->notes)
                ? trim((string) $this->notes)
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

            'product_sku_id' => [

                'required',

                'integer',

                Rule::exists(
                    'product_skus',
                    'id'
                )->whereNull('deleted_at'),
            ],

            'quantity' => [

                'required',

                'integer',

                'between:1,9999',
            ],

            'notes' => [

                'sometimes',

                'nullable',

                'string',

                'max:500',
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

            'product_sku_id.required' =>
                'Varian produk wajib dipilih.',

            'product_sku_id.exists' =>
                'Varian produk tidak ditemukan.',

            'quantity.required' =>
                'Jumlah produk wajib diisi.',

            'quantity.integer' =>
                'Jumlah produk harus berupa angka.',

            'quantity.between' =>
                'Jumlah produk harus antara 1 sampai 9.999.',

            'notes.max' =>
                'Catatan maksimal 500 karakter.',
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

            'product_sku_id' =>
                'varian produk',

            'quantity' =>
                'jumlah produk',

            'notes' =>
                'catatan',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Business Validation
    |--------------------------------------------------------------------------
    */

    public function withValidator(
        Validator $validator
    ): void {

        $validator->after(

            function (
                Validator $validator
            ) {

                if (
                    !filled($this->product_sku_id)
                ) {
                    return;
                }

                $sku = ProductSku::query()
                    ->with('inventory')
                    ->find(
                        $this->product_sku_id
                    );

                if (!$sku) {
                    return;
                }

                if (!$sku->isActive()) {

                    $validator
                        ->errors()
                        ->add(
                            'product_sku_id',
                            'Varian produk sedang tidak aktif.'
                        );

                    return;
                }

                if (!$sku->isPublished()) {

                    $validator
                        ->errors()
                        ->add(
                            'product_sku_id',
                            'Varian produk belum tersedia untuk dibeli.'
                        );

                    return;
                }

                if (!$sku->isInStock()) {

                    $validator
                        ->errors()
                        ->add(
                            'product_sku_id',
                            'Stok produk sedang habis.'
                        );

                    return;
                }

                if (
                    $this->quantity >
                    $sku->availableStock()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Jumlah melebihi stok yang tersedia.'
                        );
                }

                if (
                    $this->quantity <
                    $sku->minimum_order_quantity
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Jumlah pembelian belum memenuhi minimum pemesanan.'
                        );
                }

                if (
                    filled(
                        $sku->maximum_order_quantity
                    )
                    &&
                    $this->quantity >
                    $sku->maximum_order_quantity
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Jumlah melebihi batas maksimum pembelian.'
                        );
                }
            }
        );
    }
}