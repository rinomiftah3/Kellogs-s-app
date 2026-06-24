<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\ValidationException;

class ProductImageService
{
    /*
    |--------------------------------------------------------------------------
    | List Images
    |--------------------------------------------------------------------------
    */

    public function paginate(
        Product $product,
        int $perPage = 15
    ): LengthAwarePaginator {

        return ProductImage::query()

            ->with('product')

            ->byProduct($product->id)

            ->primaryFirst()

            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Image
    |--------------------------------------------------------------------------
    */

    public function find(
        ProductImage $image
    ): ProductImage {

        return $image->load(
            'product'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Image
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        UploadedFile $file,
        User $actor,
        Request $request
    ): ProductImage {

        return DB::transaction(

            function () use (
                $data,
                $file,
                $actor,
                $request
            ) {

                $this->validatePrimaryImage(
                    $data
                );

                $data['image_url'] =
                    $this->storeImage(
                        $file
                    );

                /*
                |--------------------------------------------------------------------------
                | Primary Image Rule
                |--------------------------------------------------------------------------
                */

                if (
                    ! empty($data['is_primary'])
                ) {

                    ProductImage::query()

                        ->where(
                            'product_id',
                            $data['product_id']
                        )

                        ->update([
                            'is_primary' => false,
                        ]);

                    $data['is_active'] = true;
                }

                /*
                |--------------------------------------------------------------------------
                | Default Sort Order
                |--------------------------------------------------------------------------
                */

                if (
                    ! isset($data['sort_order'])
                ) {

                    $data['sort_order'] =
                        ProductImage::query()

                            ->where(
                                'product_id',
                                $data['product_id']
                            )

                            ->max('sort_order') + 1;
                }

                $image = ProductImage::create(
                    $data
                );

                activity()

                    ->causedBy($actor)

                    ->performedOn($image)

                    ->event(
                        'product_image_created'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'attributes' =>
                            $image->only([

                                'id',

                                'product_id',

                                'image_url',

                                'alt_text',

                                'sort_order',

                                'is_primary',

                                'is_active',
                            ]),
                    ])

                    ->log(
                        'Product image created'
                    );

                $this->clearCaches();

                return $image->load(
                    'product'
                );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Image
    |--------------------------------------------------------------------------
    */

    public function update(
        ProductImage $image,
        array $data,
        ?UploadedFile $file,
        User $actor,
        Request $request
    ): ProductImage {

        return DB::transaction(

            function () use (
                $image,
                $data,
                $file,
                $actor,
                $request
            ) {

                $oldData = $image->only([

                    'id',

                    'product_id',

                    'image_url',

                    'alt_text',

                    'sort_order',

                    'is_primary',

                    'is_active',
                ]);

                $mergedData = array_merge(
                    [
                        'is_primary' =>
                            $image->is_primary,

                        'is_active' =>
                            $image->is_active,
                    ],
                    $data
                );

                $this->validatePrimaryImage(
                    $mergedData
                );

                /*
                |--------------------------------------------------------------------------
                | Replace File
                |--------------------------------------------------------------------------
                */

                if ($file) {

                    $this->deleteImageFile(
                        $image
                    );

                    $data['image_url'] =
                        $this->storeImage(
                            $file
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Primary Image Rule
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'is_primary',
                        $data
                    )
                    &&
                    $data['is_primary']
                ) {

                    ProductImage::query()

                        ->where(
                            'product_id',
                            $image->product_id
                        )

                        ->where(
                            'id',
                            '!=',
                            $image->id
                        )

                        ->update([
                            'is_primary' => false,
                        ]);

                    $data['is_active'] = true;
                }

                $image->update(
                    $data
                );

                activity()

                    ->causedBy($actor)

                    ->performedOn($image)

                    ->event(
                        'product_image_updated'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                        'new' =>
                            $image->fresh()
                                ->only([

                                    'id',

                                    'product_id',

                                    'image_url',

                                    'alt_text',

                                    'sort_order',

                                    'is_primary',

                                    'is_active',
                                ]),
                    ])

                    ->log(
                        'Product image updated'
                    );

                $this->clearCaches();

                return $image

                    ->fresh()

                    ->load(
                        'product'
                    );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Image
    |--------------------------------------------------------------------------
    */

    public function delete(
        ProductImage $image,
        User $actor,
        Request $request
    ): void {

        DB::transaction(

            function () use (
                $image,
                $actor,
                $request
            ) {

                $oldData = $image->only([

                    'id',

                    'product_id',

                    'image_url',

                    'alt_text',

                    'sort_order',

                    'is_primary',

                    'is_active',
                ]);

                activity()

                    ->causedBy($actor)

                    ->performedOn($image)

                    ->event(
                        'product_image_deleted'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,
                    ])

                    ->log(
                        'Product image deleted'
                    );

                /*
                |--------------------------------------------------------------------------
                | Reassign Primary Image
                |--------------------------------------------------------------------------
                */

                $isPrimary =
                    $image->isPrimary();

                $productId =
                    $image->product_id;

                $this->deleteImageFile(
                    $image
                );

                $image->delete();

                if ($isPrimary) {

                    ProductImage::query()

                        ->where(
                            'product_id',
                            $productId
                        )

                        ->ordered()

                        ->first()

                        ?->update([

                            'is_primary' => true,

                            'is_active' => true,
                        ]);
                }

                $this->clearCaches();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Set Primary Image
    |--------------------------------------------------------------------------
    */

    public function setPrimary(
        ProductImage $image,
        User $actor,
        Request $request
    ): ProductImage {

        DB::transaction(

            function () use (
                $image,
                $actor,
                $request
            ) {

                ProductImage::query()

                    ->where(
                        'product_id',
                        $image->product_id
                    )

                    ->update([

                        'is_primary' => false,
                    ]);

                $image->update([

                    'is_primary' => true,

                    'is_active' => true,
                ]);

                activity()

                    ->causedBy($actor)

                    ->performedOn($image)

                    ->event(
                        'product_image_primary_changed'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'image_id' =>
                            $image->id,
                    ])

                    ->log(
                        'Product primary image changed'
                    );

                $this->clearCaches();
            }
        );

        return $image

            ->fresh()

            ->load(
                'product'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validatePrimaryImage(
        array $data
    ): void {

        if (

            ($data['is_primary'] ?? false)

            &&

            array_key_exists(
                'is_active',
                $data
            )

            &&

            ! $data['is_active']

        ) {

            throw ValidationException::withMessages([
                'is_active' => [
                    'Gambar utama harus dalam kondisi aktif.',
                ],
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Helpers
    |--------------------------------------------------------------------------
    */

    private function storeImage(
        UploadedFile $file
    ): string {

        return $file->store(
            'product-images',
            'public'
        );
    }

    private function deleteImageFile(
        ProductImage $image
    ): void {

        if (
            ! $image->hasImage()
        ) {
            return;
        }

        if (
            Storage::disk('public')

                ->exists(
                    $image->image_url
                )
        ) {

            Storage::disk('public')

                ->delete(
                    $image->image_url
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    private function clearCaches(): void
    {
        Cache::forget(
            'dashboard.overview'
        );

        Cache::forget(
            'product.statistics'
        );
    }
}