<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * BulkRemoveCartItemsRequest
 *
 * Validates removing multiple cart items.
 *
 * Laravel 13
 * PHP 8.4
 */
class BulkRemoveCartItemsRequest extends FormRequest
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
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'item_ids' => [

                'required',

                'array',

                'min:1',
            ],

            'item_ids.*' => [

                'integer',

                'distinct',

                Rule::exists(
                    'cart_items',
                    'id'
                )->whereNull('deleted_at'),
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

            'item_ids.required' =>
                'Daftar item wajib diisi.',

            'item_ids.array' =>
                'Format item tidak valid.',

            'item_ids.min' =>
                'Minimal satu item harus dipilih.',

            'item_ids.*.integer' =>
                'ID item tidak valid.',

            'item_ids.*.distinct' =>
                'Terdapat item yang duplikat.',

            'item_ids.*.exists' =>
                'Item keranjang tidak ditemukan.',
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

            'item_ids' =>
                'daftar item',

            'item_ids.*' =>
                'item keranjang',
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

                if (
                    !is_array($this->item_ids)
                ) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Maximum Bulk Operation
                |--------------------------------------------------------------------------
                */

                if (
                    count($this->item_ids) > 100
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'item_ids',
                            'Maksimal 100 item dapat dihapus sekaligus.'
                        );
                }
            }
        );
    }
}