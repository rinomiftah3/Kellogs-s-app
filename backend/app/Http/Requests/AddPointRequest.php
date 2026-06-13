<?php

namespace App\Http\Requests;

use App\Models\PointTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * AddPointRequest
 *
 * Validates adding (crediting) loyalty points to a customer's account.
 * Supports both manual admin adjustments and system-triggered earn events.
 *
 * Laravel 13 | PHP 8.4
 */
class AddPointRequest extends FormRequest
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
        return $this->user()?->can('manage loyalty')
            ?? false;
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

            'customer_profile_id' => [
                'required',
                'integer',
                'exists:customer_profiles,id',
                Rule::exists(
                    'loyalty_points',
                    'customer_profile_id'
                ),
            ],

            'points' => [
                'required',
                'integer',
                'min:1',
                'max:1000000',
            ],

            'type' => [
                'required',
                'string',
                Rule::in([
                    PointTransaction::TYPE_EARN,
                    PointTransaction::TYPE_BONUS,
                    PointTransaction::TYPE_REFUND,
                    PointTransaction::TYPE_ADJUSTMENT,
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'order_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:orders,id',
            ],

            'expired_at' => [
                'sometimes',
                'nullable',
                'date',
                'after:today',
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
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'customer_profile_id.required' =>
                'Customer wajib dipilih.',

            'customer_profile_id.exists' =>
                'Customer yang dipilih tidak ditemukan.',

            'customer_profile_id.integer' =>
                'Customer tidak valid.',

            'points.required' =>
                'Jumlah poin wajib diisi.',

            'points.integer' =>
                'Jumlah poin harus berupa angka.',

            'points.min' =>
                'Jumlah poin minimal 1.',

            'points.max' =>
                'Jumlah poin maksimal 1.000.000.',

            'type.in' =>
                'Jenis transaksi harus berupa earn, bonus, refund, atau adjustment.',

            'title.required' =>
                'Judul transaksi wajib diisi.',

            'expired_at.after' =>
                'Tanggal kedaluwarsa poin harus setelah hari ini.',
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

            'customer_profile_id' =>
                'customer',

            'points' =>
                'jumlah poin',

            'type' =>
                'jenis transaksi',

            'title' =>
                'judul transaksi',

            'description' =>
                'deskripsi',

            'order_id' =>
                'pesanan terkait',

            'expired_at' =>
                'tanggal kedaluwarsa poin',

            'metadata' =>
                'metadata',
        ];
    }
}