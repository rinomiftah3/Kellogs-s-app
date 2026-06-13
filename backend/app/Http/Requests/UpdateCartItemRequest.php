<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * UpdateCartItemRequest
 *
 * Validates updating an existing cart item.
 *
 * Laravel 13
 * PHP 8.4
 */
class UpdateCartItemRequest extends FormRequest
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

            'quantity' => [

                'sometimes',

                'integer',

                'between:1,9999',
            ],

            'is_selected' => [

                'sometimes',

                'boolean',
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

            'quantity' =>
                'jumlah produk',

            'is_selected' =>
                'status pilihan',

            'notes' =>
                'catatan',
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
                | Minimal satu field harus diubah
                |--------------------------------------------------------------------------
                */

                if (

                    ! $this->has('quantity')
                    &&
                    ! $this->has('is_selected')
                    &&
                    ! $this->has('notes')

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'request',
                            'Tidak ada data yang diperbarui.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Quantity Business Validation
                |--------------------------------------------------------------------------
                */

                if (
                    ! $this->has('quantity')
                ) {
                    return;
                }

                $cartItem = $this->route(
                    'cart_item'
                );

                if (
                    ! $cartItem instanceof CartItem
                ) {
                    return;
                }

                $sku = $cartItem->productSku;

                if (!$sku) {
                    return;
                }

                $sku->loadMissing('inventory');

                if (!$sku) {
                    return;
                }

                if (!$sku->isActive()) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Produk sudah tidak aktif.'
                        );

                    return;
                }

                if (!$sku->isPublished()) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Produk belum tersedia untuk dibeli.'
                        );

                    return;
                }

                if (!$sku->isInStock()) {

                    $validator
                        ->errors()
                        ->add(
                            'quantity',
                            'Stok produk sedang habis.'
                        );

                    return;
                }

                $quantity = (int)
                    $this->quantity;

                if (
                    $quantity >
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
                    $quantity <
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
                    $quantity >
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