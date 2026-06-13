<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * RemoveCartItemRequest
 *
 * Validates removing one or more items from
 * the authenticated customer's cart.
 *
 * Supports:
 * - Single deletion via route model binding
 * - Batch deletion using ids[]
 *
 * Laravel 13
 * PHP 8.4
 */
class RemoveCartItemRequest extends FormRequest
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

            'ids' => [

                'sometimes',

                'array',

                'min:1',

                'max:100',
            ],

            'ids.*' => [

                'required_with:ids',

                'integer',

                'distinct',

                'exists:cart_items,id',
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

            'ids.array' =>
                'Daftar item keranjang harus berupa array.',

            'ids.min' =>
                'Minimal terdapat satu item yang dipilih.',

            'ids.max' =>
                'Maksimal 100 item dapat dihapus sekaligus.',

            'ids.*.distinct' =>
                'Terdapat item keranjang yang dipilih lebih dari satu kali.',

            'ids.*.exists' =>
                'Satu atau lebih item keranjang tidak ditemukan.',
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

            'ids' =>
                'daftar item keranjang',

            'ids.*' =>
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

                /*
                |--------------------------------------------------------------------------
                | Single delete OR batch delete required
                |--------------------------------------------------------------------------
                */

                $routeItem = $this->route(
                    'cart_item'
                );

                $batchIds = $this->input(
                    'ids',
                    []
                );

                if (
                    !$routeItem
                    &&
                    empty($batchIds)
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'request',
                            'Tidak ada item keranjang yang dipilih untuk dihapus.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Ownership Validation (Batch)
                |--------------------------------------------------------------------------
                */

                if (empty($batchIds)) {
                    return;
                }

                $customerProfileId = $this->user()
                    ?->customerProfile
                    ?->id;

                if (!$customerProfileId) {
                    return;
                }

                $ownedCount = CartItem::query()

                    ->whereIn(
                        'id',
                        $batchIds
                    )

                    ->whereHas(
                        'cart',
                        fn ($query)

                            => $query->where(
                                'customer_profile_id',
                                $customerProfileId
                            )
                    )

                    ->count();

                if (
                    $ownedCount !== count($batchIds)
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'ids',
                            'Satu atau lebih item keranjang bukan milik Anda.'
                        );
                }
            }
        );
    }
}