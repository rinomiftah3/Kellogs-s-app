<?php

namespace App\Http\Requests;

use App\Models\ProductReview;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductReviewRequest extends FormRequest
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
        return $this->user() !== null;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'title' => filled($this->title)
                ? trim((string) $this->title)
                : null,

            'review' => filled($this->review)
                ? trim((string) $this->review)
                : null,

            /*
            |--------------------------------------------------------------
            | Customer tidak boleh menentukan sendiri.
            | Ditentukan sistem.
            |--------------------------------------------------------------
            */
            'is_verified_purchase' => false,

            /*
            |--------------------------------------------------------------
            | Review baru selalu pending.
            |--------------------------------------------------------------
            */
            'status' => ProductReview::STATUS_PENDING,
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'product_id' => [

                'bail',

                'required',

                'integer',

                Rule::exists(
                    'products',
                    'id'
                ),
            ],

            'customer_profile_id' => [

                'bail',

                'required',

                'integer',

                Rule::exists(
                    'customer_profiles',
                    'id'
                ),

                Rule::unique(
                    'product_reviews'
                )->where(
                    fn ($query)

                        => $query->where(
                            'product_id',
                            $this->product_id
                        )
                ),
            ],

            'rating' => [

                'required',

                'integer',

                'between:1,5',
            ],

            'title' => [

                'nullable',

                'string',

                'max:255',
            ],

            'review' => [

                'nullable',

                'string',
            ],

            'images' => [

                'nullable',

                'array',

                'max:5',
            ],

            'images.*' => [

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',
            ],

            'is_verified_purchase' => [

                'boolean',
            ],

            'status' => [

                Rule::in([
                    ProductReview::STATUS_PENDING,
                ]),
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

            'customer_profile_id.required' =>
                'Customer wajib dipilih.',

            'customer_profile_id.exists' =>
                'Customer tidak ditemukan.',

            'customer_profile_id.unique' =>
                'Anda sudah pernah memberikan review untuk produk ini.',

            'rating.required' =>
                'Rating wajib diisi.',

            'rating.integer' =>
                'Rating harus berupa angka.',

            'rating.between' =>
                'Rating harus antara 1 sampai 5.',

            'title.max' =>
                'Judul review maksimal 255 karakter.',

            'images.array' =>
                'Format gambar review tidak valid.',

            'images.max' =>
                'Maksimal 5 gambar review.',

            'images.*.image' =>
                'File harus berupa gambar.',

            'images.*.mimes' =>
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.',

            'images.*.max' =>
                'Ukuran setiap gambar maksimal 2 MB.',
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

            'customer_profile_id' =>
                'customer',

            'rating' =>
                'rating',

            'title' =>
                'judul review',

            'review' =>
                'isi review',

            'images' =>
                'gambar review',
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

                /*
                |----------------------------------------------------------
                | Minimal title atau review harus terisi.
                |----------------------------------------------------------
                */

                if (

                    blank($this->title)

                    &&

                    blank($this->review)
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'review',
                            'Judul atau isi review wajib diisi.'
                        );
                }
            }
        );
    }
}