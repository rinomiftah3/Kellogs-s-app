<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductSkuRequest extends FormRequest
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
        return $this->user()?->can(
            'products.update'
        ) ?? false;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'sku' => strtoupper(
                trim(
                    (string) $this->input('sku')
                )
            ),

            'barcode' => filled(
                $this->barcode
            )
                ? trim(
                    (string) $this->barcode
                )
                : null,

            'is_default' => $this->has('is_default')
                ? filter_var(
                    $this->input('is_default'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input('is_default'),

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input('is_active'),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $sku = $this->route('product_sku')
            ?? $this->route('sku');

        $skuId = $sku instanceof ProductSku
            ? $sku->id
            : $sku;

        return [

            'sku' => [

                'bail',

                'required',

                'string',

                'max:100',

                Rule::unique(
                    'product_skus',
                    'sku'
                )->ignore(
                    $skuId
                ),
            ],

            'barcode' => [

                'nullable',

                'string',

                'max:100',

                Rule::unique(
                    'product_skus',
                    'barcode'
                )->ignore(
                    $skuId
                ),
            ],

            'price' => [

                'required',

                'numeric',

                'min:0',
            ],

            'compare_at_price' => [

                'nullable',

                'numeric',

                'min:0',
            ],

            'cost_price' => [

                'nullable',

                'numeric',

                'min:0',
            ],

            'weight' => [

                'required',

                'numeric',

                'min:0',
            ],

            'length' => [

                'nullable',

                'numeric',

                'min:0',
            ],

            'width' => [

                'nullable',

                'numeric',

                'min:0',
            ],

            'height' => [

                'nullable',

                'numeric',

                'min:0',
            ],

            'minimum_order_quantity' => [

                'required',

                'integer',

                'min:1',
            ],

            'maximum_order_quantity' => [

                'nullable',

                'integer',

                'min:1',
            ],

            'option_value_ids' => [

                'nullable',

                'array',
            ],

            'option_value_ids.*' => [

                'integer',

                Rule::exists(
                    'product_option_values',
                    'id'
                ),
            ],

            'is_default' => [

                'sometimes',

                'boolean',
            ],

            'status' => [

                'required',

                'string',

                Rule::in([

                    ProductSku::STATUS_DRAFT,

                    ProductSku::STATUS_ACTIVE,

                    ProductSku::STATUS_INACTIVE,

                    ProductSku::STATUS_ARCHIVED,
                ]),
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],

            'published_at' => [

                'nullable',

                'date',
            ],

            'stock' => [

            'nullable',

            'integer',

            'min:0',
        ],

        'minimum_stock' => [

            'nullable',

            'integer',

            'min:0',
        ],

        'maximum_stock' => [

            'nullable',

            'integer',

            'min:0',
        ],

        'reorder_point' => [

            'nullable',

            'integer',

            'min:0',
        ],

        'allow_backorder' => [

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

            'product_id.required' =>
                'Produk wajib dipilih.',

            'product_id.exists' =>
                'Produk tidak ditemukan.',

            'sku.required' =>
                'SKU wajib diisi.',

            'sku.unique' =>
                'SKU sudah digunakan.',

            'sku.max' =>
                'SKU maksimal 100 karakter.',

            'barcode.unique' =>
                'Barcode sudah digunakan.',

            'barcode.max' =>
                'Barcode maksimal 100 karakter.',

            'price.required' =>
                'Harga wajib diisi.',

            'price.numeric' =>
                'Harga harus berupa angka.',

            'price.min' =>
                'Harga tidak boleh negatif.',

            'compare_at_price.numeric' =>
                'Harga coret harus berupa angka.',

            'cost_price.numeric' =>
                'Harga modal harus berupa angka.',

            'weight.required' =>
                'Berat wajib diisi.',

            'weight.numeric' =>
                'Berat harus berupa angka.',

            'minimum_order_quantity.required' =>
                'Minimum pembelian wajib diisi.',

            'minimum_order_quantity.min' =>
                'Minimum pembelian minimal 1.',

            'maximum_order_quantity.min' =>
                'Maksimum pembelian minimal 1.',

            'option_value_ids.array' =>
                'Variasi produk tidak valid.',

            'option_value_ids.*.exists' =>
                'Variasi produk tidak ditemukan.',

            'status.in' =>
                'Status SKU tidak valid.',

            'is_default.boolean' =>
                'Status SKU default tidak valid.',

            'is_active.boolean' =>
                'Status aktif tidak valid.',

            'published_at.date' =>
                'Tanggal publish tidak valid.',

            'stock.integer'
                => 'Stok harus berupa angka.',

            'stock.min'
                => 'Stok tidak boleh negatif.',

            'minimum_stock.integer'
                => 'Minimum stok harus berupa angka.',

            'reorder_point.integer'
                => 'Reorder point harus berupa angka.',

            'allow_backorder.boolean'
                => 'Status backorder tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'product_id' =>
                'produk',

            'sku' =>
                'SKU',

            'barcode' =>
                'barcode',

            'price' =>
                'harga',

            'compare_at_price' =>
                'harga coret',

            'cost_price' =>
                'harga modal',

            'weight' =>
                'berat',

            'length' =>
                'panjang',

            'width' =>
                'lebar',

            'height' =>
                'tinggi',

            'minimum_order_quantity' =>
                'minimum pembelian',

            'maximum_order_quantity' =>
                'maksimum pembelian',

            'option_value_ids' =>
                'variasi produk',

            'status' =>
                'status',

            'published_at' =>
                'tanggal publish',
            
            'stock'
                => 'stok',

            'minimum_stock'
                => 'minimum stok',

            'maximum_stock'
                => 'maksimum stok',

            'reorder_point'
                => 'reorder point',

            'allow_backorder'
                => 'backorder',
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

                if (

                    filled($this->compare_at_price)

                    &&

                    $this->compare_at_price
                        <= $this->price
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'compare_at_price',
                            'Harga coret harus lebih besar dari harga jual.'
                        );
                }

                if (

                    filled($this->cost_price)

                    &&

                    $this->cost_price
                        > $this->price
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'cost_price',
                            'Harga modal tidak boleh melebihi harga jual.'
                        );
                }

                if (

                    filled(
                        $this->maximum_order_quantity
                    )

                    &&

                    $this->maximum_order_quantity
                        < $this->minimum_order_quantity
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'maximum_order_quantity',
                            'Maksimum pembelian harus lebih besar atau sama dengan minimum pembelian.'
                        );
                }

                if (

                    $this->boolean('is_default')

                    &&

                    $this->has('is_active')

                    &&

                    ! $this->boolean('is_active')
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'is_active',
                            'SKU default harus berstatus aktif.'
                        );
                }
            }
        );
    }
}