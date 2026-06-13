<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * RedeemPointRequest
 *
 * Validates a customer's request to redeem loyalty points,
 * typically applied during checkout as a discount.
 *
 * Laravel 13 | PHP 8.4
 */
class RedeemPointRequest extends FormRequest
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
        $user = $this->user();

        return $user?->hasRole('customer')
            || $user?->can('manage loyalty')
            || false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && $this->metadata === '') {
            $this->merge([
                'metadata' => null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'points' => [
                'required',
                'integer',
                'min:1',
                'max:1000000',
            ],

            'session_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                'required_without:order_id',
                'exists:checkout_sessions,session_code',
            ],

            'order_id' => [
                'sometimes',
                'nullable',
                'integer',
                'required_without:session_code',
                'exists:orders,id',
            ],

            'title' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'metadata.*' => [
                'nullable',
            ],
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

        $validator->after(function (
            Validator $validator
        ) {
            $user = $this->user();

            if (
                $user?->hasRole('customer')
                && ! $user->customerProfile?->loyaltyPoint
            ) {
                $validator->errors()->add(
                    'points',
                    'You do not have an active loyalty account.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'points.required' =>
                'Jumlah poin yang akan ditukar wajib diisi.',

            'points.integer' =>
                'Jumlah poin harus berupa angka.',

            'points.min' =>
                'Minimal penukaran adalah 1 poin.',

            'points.max' =>
                'Maksimal penukaran adalah 1.000.000 poin.',

            'session_code.required_without' =>
                'Session checkout atau order wajib dipilih.',

            'session_code.exists' =>
                'Session checkout tidak ditemukan.',

            'order_id.required_without' =>
                'Session checkout atau order wajib dipilih.',

            'order_id.exists' =>
                'Order tidak ditemukan.',
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

            'points' =>
                'jumlah poin',

            'session_code' =>
                'session checkout',

            'order_id' =>
                'order',

            'title' =>
                'judul transaksi',

            'description' =>
                'deskripsi',

            'metadata' =>
                'metadata',
        ];
    }
}