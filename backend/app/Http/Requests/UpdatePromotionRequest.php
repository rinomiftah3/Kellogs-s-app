<?php

namespace App\Http\Requests;

use App\Models\Promotion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePromotionRequest extends FormRequest
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
        return $this->user()?->can('manage promotions')
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
        $routePromotion = $this->route('promotion');

        $promotionId = $routePromotion instanceof Promotion
            ? $routePromotion->getKey()
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
                Rule::unique('promotions', 'code')
                    ->ignore($promotionId),
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
                    Promotion::TYPE_FIXED_DISCOUNT,
                    Promotion::TYPE_PERCENTAGE_DISCOUNT,
                    Promotion::TYPE_FLASH_SALE,
                    Promotion::TYPE_BUY_X_GET_Y,
                    Promotion::TYPE_FREE_SHIPPING,
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

            'buy_quantity' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'free_quantity' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'usage_limit' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'is_featured' => [
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

            'banner_image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
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
                'Kode promosi sudah digunakan.',

            'code.alpha_dash' =>
                'Kode promosi hanya boleh berisi huruf, angka, dash, dan underscore.',

            'type.in' =>
                'Tipe promosi tidak valid.',
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

            'name' => 'nama promosi',
            'code' => 'kode promosi',
            'description' => 'deskripsi',
            'type' => 'tipe promosi',
            'discount_value' => 'nilai diskon',
            'maximum_discount' => 'maksimum diskon',
            'minimum_purchase' => 'minimum pembelian',
            'buy_quantity' => 'jumlah beli',
            'free_quantity' => 'jumlah gratis',
            'usage_limit' => 'batas penggunaan',
            'is_active' => 'status aktif',
            'is_featured' => 'status unggulan',
            'is_stackable' => 'status stackable',
            'start_at' => 'tanggal mulai',
            'end_at' => 'tanggal berakhir',
            'banner_image' => 'gambar banner',
            'sort_order' => 'urutan tampil',
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

                /** @var Promotion|null $promotion */
                $promotion = $this->route('promotion');

                /*
                |--------------------------------------------------------------------------
                | Effective Values
                |--------------------------------------------------------------------------
                */

                $type = $this->input(
                    'type',
                    $promotion?->type
                );

                $discountValue = (float) $this->input(
                    'discount_value',
                    $promotion?->discount_value ?? 0
                );

                $maximumDiscount = $this->input(
                    'maximum_discount',
                    $promotion?->maximum_discount
                );

                $buyQuantity = $this->input(
                    'buy_quantity',
                    $promotion?->buy_quantity
                );

                $freeQuantity = $this->input(
                    'free_quantity',
                    $promotion?->free_quantity
                );

                $startAt = $this->input(
                    'start_at',
                    $promotion?->start_at
                );

                $endAt = $this->input(
                    'end_at',
                    $promotion?->end_at
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
                | Percentage & Flash Sale
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $type,
                        [
                            Promotion::TYPE_PERCENTAGE_DISCOUNT,
                            Promotion::TYPE_FLASH_SALE,
                        ],
                        true
                    )
                ) {

                    if (
                        $discountValue > 100
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
                                'Maksimum diskon wajib diisi.'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Fixed Discount
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Promotion::TYPE_FIXED_DISCOUNT
                    &&
                    $maximumDiscount !== null
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'maximum_discount',
                            'Fixed discount tidak boleh memiliki maksimum diskon.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Buy X Get Y
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Promotion::TYPE_BUY_X_GET_Y
                ) {

                    if (
                        empty($buyQuantity)
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'buy_quantity',
                                'Jumlah beli wajib diisi.'
                            );
                    }

                    if (
                        empty($freeQuantity)
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'free_quantity',
                                'Jumlah gratis wajib diisi.'
                            );
                    }

                    if (
                        $discountValue != 0
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Buy X Get Y harus memiliki nilai diskon 0.'
                            );
                    }

                    if (
                        $maximumDiscount !== null
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'maximum_discount',
                                'Buy X Get Y tidak boleh memiliki maksimum diskon.'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Free Shipping
                |--------------------------------------------------------------------------
                */

                if (
                    $type === Promotion::TYPE_FREE_SHIPPING
                ) {

                    if (
                        $discountValue != 0
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'discount_value',
                                'Gratis ongkir harus memiliki nilai diskon 0.'
                            );
                    }

                    if (
                        $maximumDiscount !== null
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'maximum_discount',
                                'Gratis ongkir tidak boleh memiliki maksimum diskon.'
                            );
                    }
                }
            }
        );
    }
}