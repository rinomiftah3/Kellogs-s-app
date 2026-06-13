<?php

namespace App\Http\Requests;

use App\Models\ProductReview;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductReviewRequest extends FormRequest
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

            'moderation_notes' => filled(
                $this->moderation_notes
            )
                ? trim(
                    (string) $this->moderation_notes
                )
                : null,
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

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

            'status' => [

                'nullable',

                'string',

                Rule::in([

                    ProductReview::STATUS_PENDING,

                    ProductReview::STATUS_APPROVED,

                    ProductReview::STATUS_REJECTED,
                ]),
            ],

            'moderation_notes' => [

                'nullable',

                'string',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'rating.required' =>
                'Rating wajib diisi.',

            'rating.integer' =>
                'Rating harus berupa angka.',

            'rating.between' =>
                'Rating harus antara 1 sampai 5.',

            'title.max' =>
                'Judul review maksimal 255 karakter.',

            'status.in' =>
                'Status review tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'rating' =>
                'rating',

            'title' =>
                'judul review',

            'review' =>
                'isi review',

            'status' =>
                'status review',

            'moderation_notes' =>
                'catatan moderasi',
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
                |--------------------------------------------------------------------------
                | Minimal title atau review harus terisi
                |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | Moderation notes wajib saat reject
                |--------------------------------------------------------------------------
                */

                if (

                    $this->status ===
                    ProductReview::STATUS_REJECTED

                    &&

                    blank(
                        $this->moderation_notes
                    )
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'moderation_notes',
                            'Catatan moderasi wajib diisi saat review ditolak.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Moderation notes tidak boleh diisi selain rejected
                |--------------------------------------------------------------------------
                */

                if (

                    filled(
                        $this->moderation_notes
                    )

                    &&

                    $this->status !==
                    ProductReview::STATUS_REJECTED
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'moderation_notes',
                            'Catatan moderasi hanya boleh diisi untuk review yang ditolak.'
                        );
                }
            }
        );
    }
}