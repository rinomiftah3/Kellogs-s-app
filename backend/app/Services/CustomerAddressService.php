<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerAddressService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'customer',
    ];

    /**
     * Get paginated addresses.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return CustomerAddress::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['province'] ?? null),
                fn ($query) => $query->byProvince(
                    $filters['province']
                )
            )

            ->when(
                filled($filters['city'] ?? null),
                fn ($query) => $query->byCity(
                    $filters['city']
                )
            )

            ->when(
                filled($filters['postal_code'] ?? null),
                fn ($query) => $query->byPostalCode(
                    $filters['postal_code']
                )
            )

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            /*
            |--------------------------------------------------------------------------
            | Default Filter
            |--------------------------------------------------------------------------
            */

            ->when(
                array_key_exists(
                    'is_default',
                    $filters
                ),
                fn ($query) => $filters['is_default']
                    ? $query->default()
                    : $query->where(
                        'is_default',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->inactive()
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all addresses.
     */
    public function all(
        array $filters = []
    ): Collection {

        return CustomerAddress::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['province'] ?? null),
                fn ($query) => $query->byProvince(
                    $filters['province']
                )
            )

            ->when(
                filled($filters['city'] ?? null),
                fn ($query) => $query->byCity(
                    $filters['city']
                )
            )

            ->when(
                filled($filters['postal_code'] ?? null),
                fn ($query) => $query->byPostalCode(
                    $filters['postal_code']
                )
            )

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            /*
            |--------------------------------------------------------------------------
            | Default Filter
            |--------------------------------------------------------------------------
            */

            ->when(
                array_key_exists(
                    'is_default',
                    $filters
                ),
                fn ($query) => $filters['is_default']
                    ? $query->default()
                    : $query->where(
                        'is_default',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->inactive()
            )

            ->latest()

            ->get();
    }

    /**
     * Find address by ID.
     */
    public function find(
        int $id
    ): ?CustomerAddress {

        return CustomerAddress::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find address or fail.
     */
    public function findOrFail(
        CustomerAddress|int $address
    ): CustomerAddress {

        if (
            $address instanceof CustomerAddress
        ) {

            return $address->load(
                $this->relations
            );
        }

        return CustomerAddress::query()

            ->with($this->relations)

            ->findOrFail($address);
    }
    /**
     * Get addresses by customer.
     */
    public function getByCustomer(
        CustomerProfile|int $customer,
        bool $activeOnly = true
    ): Collection {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        return CustomerAddress::query()

            ->with($this->relations)

            ->byCustomer($customerId)

            ->when(
                $activeOnly,
                fn ($query) => $query->active()
            )

            ->orderByDesc('is_default')

            ->latest()

            ->get();
    }

    /**
     * Get default address by customer.
     */
    public function getDefaultAddress(
        CustomerProfile|int $customer
    ): ?CustomerAddress {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        return CustomerAddress::query()

            ->with($this->relations)

            ->byCustomer($customerId)

            ->default()

            ->first();
    }

    /**
     * Create address.
     */
    public function create(
        array $data
    ): CustomerAddress {

        return DB::transaction(
            function () use ($data) {

                CustomerProfile::query()
                    ->findOrFail(
                        $data['customer_profile_id']
                    );

                /*
                |--------------------------------------------------------------------------
                | First Address Rule
                |--------------------------------------------------------------------------
                | Jika customer belum memiliki alamat,
                | alamat pertama otomatis menjadi default.
                |--------------------------------------------------------------------------
                */

                $hasAddress = CustomerAddress::query()

                    ->where(
                        'customer_profile_id',
                        $data['customer_profile_id']
                    )

                    ->exists();

                $isDefault = (bool) (

                    $data['is_default']

                    ??

                    ! $hasAddress
                );

                /*
                |--------------------------------------------------------------------------
                | Only One Default Address
                |--------------------------------------------------------------------------
                */

                if ($isDefault) {

                    $this->clearDefaultAddresses(
                        $data['customer_profile_id']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create Address
                |--------------------------------------------------------------------------
                */

                $address = CustomerAddress::create([

                    'customer_profile_id'
                        => $data['customer_profile_id'],

                    'label'
                        => trim(
                            $data['label']
                        ),

                    'recipient_name'
                        => trim(
                            $data['recipient_name']
                        ),

                    'recipient_phone'
                        => trim(
                            $data['recipient_phone']
                        ),

                    'address'
                        => trim(
                            $data['address']
                        ),

                    'province'
                        => trim(
                            $data['province']
                        ),

                    'city'
                        => trim(
                            $data['city']
                        ),

                    'district'
                        => trim(
                            $data['district']
                        ),

                    'subdistrict'
                        => trim(
                            $data['subdistrict']
                        ),

                    'postal_code'
                        => trim(
                            $data['postal_code']
                        ),

                    'latitude'
                        => $data['latitude']
                        ?? null,

                    'longitude'
                        => $data['longitude']
                        ?? null,

                    'is_default'
                        => $isDefault,

                    'is_active'
                        => $data['is_active']
                        ?? true,

                    'notes'
                        => $data['notes']
                        ?? null,
                ]);

                return $address

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Update address.
     */
    public function update(
        CustomerAddress|int $address,
        array $data
    ): CustomerAddress {

        return DB::transaction(
            function () use (
                $address,
                $data
            ) {

                $address = $this->findOrFail(
                    $address
                );

                /*
                |--------------------------------------------------------------------------
                | Only One Default Address
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'is_default',
                        $data
                    )
                    &&
                    (bool) $data['is_default']
                ) {

                    $this->clearDefaultAddresses(
                        $address->customer_profile_id,
                        $address->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                | customer_profile_id tidak boleh diubah.
                | Jika ingin memindahkan alamat ke customer lain,
                | maka harus delete lalu create ulang.
                |--------------------------------------------------------------------------
                */

                $address->update([

                    'label'
                        => array_key_exists(
                            'label',
                            $data
                        )
                        ? trim(
                            $data['label']
                        )
                        : $address->label,

                    'recipient_name'
                        => array_key_exists(
                            'recipient_name',
                            $data
                        )
                        ? trim(
                            $data['recipient_name']
                        )
                        : $address->recipient_name,

                    'recipient_phone'
                        => array_key_exists(
                            'recipient_phone',
                            $data
                        )
                        ? trim(
                            $data['recipient_phone']
                        )
                        : $address->recipient_phone,

                    'address'
                        => array_key_exists(
                            'address',
                            $data
                        )
                        ? trim(
                            $data['address']
                        )
                        : $address->address,

                    'province'
                        => array_key_exists(
                            'province',
                            $data
                        )
                        ? trim(
                            $data['province']
                        )
                        : $address->province,

                    'city'
                        => array_key_exists(
                            'city',
                            $data
                        )
                        ? trim(
                            $data['city']
                        )
                        : $address->city,

                    'district'
                        => array_key_exists(
                            'district',
                            $data
                        )
                        ? trim(
                            $data['district']
                        )
                        : $address->district,

                    'subdistrict'
                        => array_key_exists(
                            'subdistrict',
                            $data
                        )
                        ? trim(
                            $data['subdistrict']
                        )
                        : $address->subdistrict,

                    'postal_code'
                        => array_key_exists(
                            'postal_code',
                            $data
                        )
                        ? trim(
                            $data['postal_code']
                        )
                        : $address->postal_code,

                    'latitude'
                        => array_key_exists(
                            'latitude',
                            $data
                        )
                        ? $data['latitude']
                        : $address->latitude,

                    'longitude'
                        => array_key_exists(
                            'longitude',
                            $data
                        )
                        ? $data['longitude']
                        : $address->longitude,

                    'is_default'
                        => array_key_exists(
                            'is_default',
                            $data
                        )
                        ? (bool) $data['is_default']
                        : $address->is_default,

                    'is_active'
                        => array_key_exists(
                            'is_active',
                            $data
                        )
                        ? (bool) $data['is_active']
                        : $address->is_active,

                    'notes'
                        => array_key_exists(
                            'notes',
                            $data
                        )
                        ? $data['notes']
                        : $address->notes,
                ]);

                return $address

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Delete address.
     */
    public function delete(
        CustomerAddress|int $address
    ): bool {

        return DB::transaction(
            function () use ($address) {

                $address = $this->findOrFail(
                    $address
                );

                $customerId = $address->customer_profile_id;

                $wasDefault = $address->is_default;

                $deleted = (bool)
                    $address->delete();

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                | Jika default address dihapus,
                | maka alamat aktif lainnya otomatis
                | menjadi default.
                |--------------------------------------------------------------------------
                */

                if (
                    $deleted
                    &&
                    $wasDefault
                ) {

                    CustomerAddress::query()

                        ->where(
                            'customer_profile_id',
                            $customerId
                        )

                        ->active()

                        ->first()

                        ?->update([
                            'is_default' => true,
                        ]);
                }

                return $deleted;
            }
        );
    }

    /**
     * Activate address.
     */
    public function activate(
        CustomerAddress|int $address
    ): CustomerAddress {

        return $this->update(
            $address,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate address.
     */
    public function deactivate(
        CustomerAddress|int $address
    ): CustomerAddress {

        return $this->update(
            $address,
            [
                'is_active' => false,
            ]
        );
    }

    /**
     * Set address as default.
     */
    public function setDefault(
        CustomerAddress|int $address
    ): CustomerAddress {

        return DB::transaction(
            function () use ($address) {

                $address = $this->findOrFail(
                    $address
                );

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                | Alamat nonaktif tidak boleh
                | dijadikan default.
                |--------------------------------------------------------------------------
                */

                if (
                    ! $address->is_active
                ) {

                    throw new \RuntimeException(
                        'Alamat nonaktif tidak dapat dijadikan default.'
                    );
                }

                $this->clearDefaultAddresses(
                    $address->customer_profile_id,
                    $address->id
                );

                $address->update([

                    'is_default' => true,
                ]);

                return $address

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Clear other default addresses
     * for the same customer.
     */
    protected function clearDefaultAddresses(
        int $customerId,
        ?int $exceptId = null
    ): void {

        CustomerAddress::query()

            ->where(
                'customer_profile_id',
                $customerId
            )

            ->where(
                'is_default',
                true
            )

            ->when(
                filled($exceptId),
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $exceptId
                )
            )

            ->update([
                'is_default' => false,
            ]);
    }
}