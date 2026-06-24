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

                'sometimes',

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

/*
|--------------------------------------------------------------------------
| Images
|--------------------------------------------------------------------------
|
| Jika field images dikirim,
| seluruh gambar lama akan diganti.
|
*/

            'images' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
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

            'images.array' =>
                'Format gambar review tidak valid.',

            'images.max' =>
                'Maksimal 5 gambar review.',

            'images.*.image' =>
                'File harus berupa gambar.',

            'images.*.mimes' =>
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.',

            'images.*.max' =>
                'Ukuran gambar maksimal 2 MB.',
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
            }
        );
    }
}