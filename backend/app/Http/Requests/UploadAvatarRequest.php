<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [

            'avatar' => [

                'required',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:' . env(
                    'MAX_AVATAR_SIZE',
                    2048
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'avatar.required'
                => 'Avatar wajib diupload.',

            'avatar.image'
                => 'Avatar harus berupa gambar.',

            'avatar.mimes'
                => 'Avatar harus JPG, JPEG, PNG, atau WEBP.',

            'avatar.max'
                => 'Ukuran avatar terlalu besar.',
        ];
    }
}