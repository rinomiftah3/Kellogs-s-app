<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\CustomerProfile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'usages',
    ];

    /**
     * Get paginated vouchers.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Voucher::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['type'] ?? null),
                fn ($query) => $query->type(
                    $filters['type']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->where(
                        'is_active',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_public',
                    $filters
                ),
                fn ($query) => $filters['is_public']
                    ? $query->public()
                    : $query->where(
                        'is_public',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'valid',
                    $filters
                ),
                fn ($query) => $filters['valid']
                    ? $query->valid()
                    : $query->whereNot(
                        fn ($q) => $q->valid()
                    )
            )

            ->when(
                array_key_exists(
                    'expired',
                    $filters
                ),
                fn ($query) => $filters['expired']
                    ? $query->expired()
                    : $query->where(
                        fn ($q) => $q
                            ->whereNull('end_at')
                            ->orWhere(
                                'end_at',
                                '>=',
                                now()
                            )
                    )
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all vouchers.
     */
    public function all(
        array $filters = []
    ): Collection {

        return Voucher::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['type'] ?? null),
                fn ($query) => $query->type(
                    $filters['type']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->where(
                        'is_active',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_public',
                    $filters
                ),
                fn ($query) => $filters['is_public']
                    ? $query->public()
                    : $query->where(
                        'is_public',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'valid',
                    $filters
                ),
                fn ($query) => $filters['valid']
                    ? $query->valid()
                    : $query->whereNot(
                        fn ($q) => $q->valid()
                    )
            )

            ->when(
                array_key_exists(
                    'expired',
                    $filters
                ),
                fn ($query) => $filters['expired']
                    ? $query->expired()
                    : $query->where(
                        fn ($q) => $q
                            ->whereNull('end_at')
                            ->orWhere(
                                'end_at',
                                '>=',
                                now()
                            )
                    )
            )

            ->latest()

            ->get();
    }

    /**
     * Find voucher by ID.
     */
    public function find(
        int $id
    ): ?Voucher {

        return Voucher::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find voucher or fail.
     */
    public function findOrFail(
        int $id
    ): Voucher {

        return Voucher::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Find voucher by code.
     */
    public function findByCode(
        string $code
    ): ?Voucher {

        return Voucher::query()

            ->with($this->relations)

            ->where(
                'code',
                strtoupper(
                    trim($code)
                )
            )

            ->first();
    }
    /**
     * Create voucher.
     */
    public function create(
        array $data
    ): Voucher {

        $this->validateVoucherData(
            $data
        );

        return DB::transaction(
            function () use ($data) {

                $code = strtoupper(
                    trim($data['code'])
                );

                if (
                    Voucher::query()
                        ->where('code', $code)
                        ->exists()
                ) {

                    throw ValidationException::withMessages([

                        'code' => [
                            'Kode voucher sudah digunakan.',
                        ],
                    ]);
                }

                $voucher = Voucher::create([

                    'name'
                        => $data['name'],

                    'code'
                        => $code,

                    'description'
                        => $data['description']
                        ?? null,

                    'type'
                        => $data['type'],

                    'discount_value'
                        => $data['discount_value'],

                    'maximum_discount'
                        => $data['maximum_discount']
                        ?? null,

                    'minimum_purchase'
                        => $data['minimum_purchase']
                        ?? 0,

                    'usage_limit'
                        => $data['usage_limit']
                        ?? null,

                    'usage_per_user'
                        => $data['usage_per_user']
                        ?? 1,

                    'used_count'
                        => 0,

                    'is_active'
                        => $data['is_active']
                        ?? true,

                    'is_public'
                        => $data['is_public']
                        ?? true,

                    'is_stackable'
                        => $data['is_stackable']
                        ?? false,

                    'start_at'
                        => $data['start_at'],

                    'end_at'
                        => $data['end_at'],

                    'metadata'
                        => $data['metadata']
                        ?? null,
                ]);

                return $voucher

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Update voucher.
     */
    public function update(
        Voucher|int $voucher,
        array $data
    ): Voucher {

        return DB::transaction(
            function () use (
                $voucher,
                $data
            ) {

                $voucher = $voucher instanceof Voucher
                    ? $voucher
                    : $this->findOrFail(
                        $voucher
                    );

                $code = array_key_exists(
                    'code',
                    $data
                )
                    ? strtoupper(
                        trim(
                            $data['code']
                        )
                    )
                    : $voucher->code;

                if (

                    Voucher::query()

                        ->where(
                            'code',
                            $code
                        )

                        ->where(
                            'id',
                            '!=',
                            $voucher->id
                        )

                        ->exists()

                ) {

                    throw ValidationException::withMessages([

                        'code' => [
                            'Kode voucher sudah digunakan.',
                        ],
                    ]);
                }

                $this->validateVoucherData(
                    array_merge(
                        $voucher->toArray(),
                        $data,
                        [
                            'code' => $code,
                            'used_count'
                                => $voucher->used_count,
                        ]
                    )
                );

                $voucher->update([

                    'name'
                        => $data['name']
                        ?? $voucher->name,

                    'code'
                        => $code,

                    'description'
                        => array_key_exists(
                            'description',
                            $data
                        )
                            ? $data['description']
                            : $voucher->description,

                    'type'
                        => $data['type']
                        ?? $voucher->type,

                    'discount_value'
                        => $data['discount_value']
                        ?? $voucher->discount_value,

                    'maximum_discount'
                        => array_key_exists(
                            'maximum_discount',
                            $data
                        )
                            ? $data['maximum_discount']
                            : $voucher->maximum_discount,

                    'minimum_purchase'
                        => $data['minimum_purchase']
                        ?? $voucher->minimum_purchase,

                    'usage_limit'
                        => array_key_exists(
                            'usage_limit',
                            $data
                        )
                            ? $data['usage_limit']
                            : $voucher->usage_limit,

                    'usage_per_user'
                        => $data['usage_per_user']
                        ?? $voucher->usage_per_user,

                    'is_active'
                        => $data['is_active']
                        ?? $voucher->is_active,

                    'is_public'
                        => $data['is_public']
                        ?? $voucher->is_public,

                    'is_stackable'
                        => $data['is_stackable']
                        ?? $voucher->is_stackable,

                    'start_at'
                        => $data['start_at']
                        ?? $voucher->start_at,

                    'end_at'
                        => $data['end_at']
                        ?? $voucher->end_at,

                    'metadata'
                        => array_key_exists(
                            'metadata',
                            $data
                        )
                            ? $data['metadata']
                            : $voucher->metadata,
                ]);

                return $voucher

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Delete voucher.
     */
    public function delete(
        Voucher|int $voucher
    ): bool {

        return DB::transaction(
            function () use ($voucher) {

                $voucher = $voucher instanceof Voucher
                    ? $voucher
                    : $this->findOrFail(
                        $voucher
                    );

                return (bool)
                    $voucher->delete();
            }
        );
    }

    /**
     * Activate voucher.
     */
    public function activate(
        Voucher|int $voucher
    ): Voucher {

        return $this->update(
            $voucher,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate voucher.
     */
    public function deactivate(
        Voucher|int $voucher
    ): Voucher {

        return $this->update(
            $voucher,
            [
                'is_active' => false,
            ]
        );
    }
    /**
     * Validate voucher.
     */
    public function validateVoucher(
        Voucher|string $voucher,
        CustomerProfile|int|null $customer,
        float $subtotal
    ): Voucher {

        $voucher = $voucher instanceof Voucher
            ? $voucher
            : $this->findByCode(
                $voucher
            );

        if (! $voucher) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Voucher tidak ditemukan.',
                ],
            ]);
        }

        $this->validateVoucherAvailability(
            $voucher,
            $subtotal
        );

        if ($customer !== null) {

            $customerId = $customer instanceof CustomerProfile
                ? $customer->id
                : $customer;

            $this->validateCustomerUsage(
                $voucher,
                $customerId
            );
        }

        return $voucher;
    }

    /**
     * Calculate voucher discount.
     */
    public function calculateDiscount(
        Voucher|string $voucher,
        float $subtotal
    ): float {

        $voucher = $voucher instanceof Voucher
            ? $voucher
            : $this->findByCode(
                $voucher
            );

        if (! $voucher) {
            return 0;
        }

        try {

            $this->validateVoucherAvailability(
                $voucher,
                $subtotal
            );

        } catch (
            ValidationException
        ) {

            return 0;
        }

        return (float)

            $voucher->calculateDiscount(
                $subtotal
            );
    }

    /**
     * Apply voucher.
     */
    public function applyVoucher(
        Voucher|string $voucher,
        CustomerProfile|int|null $customer,
        float $subtotal
    ): array {

        $voucher = $this->validateVoucher(
            $voucher,
            $customer,
            $subtotal
        );

        $discount = (float)

            $voucher->calculateDiscount(
                $subtotal
            );

        return array_merge(

            $this->buildUsageSnapshot(
                $voucher,
                $subtotal,
                $discount
            ),

            [

                'voucher_type'
                    => $voucher->type,

                'is_stackable'
                    => $voucher->is_stackable,

                'voucher'
                    => $voucher,
            ]
        );
    }
    /**
     * Reserve voucher.
     */
    public function reserveVoucher(
        Voucher|string $voucher,
        CustomerProfile|int $customer,
        float $subtotal,
        ?int $orderId = null,
        array $metadata = [],
        ?string $notes = null
    ): VoucherUsage {

        return DB::transaction(
            function () use (
                $voucher,
                $customer,
                $subtotal,
                $orderId,
                $metadata,
                $notes
            ) {

                $customerId = $customer instanceof CustomerProfile
                    ? $customer->id
                    : $customer;

                $voucher = $this->validateVoucher(
                    $voucher,
                    $customerId,
                    $subtotal
                );

                $discount = (float)

                    $voucher->calculateDiscount(
                        $subtotal
                    );

                $snapshot = $this->buildUsageSnapshot(
                    $voucher,
                    $subtotal,
                    $discount
                );

                $usage = VoucherUsage::create([

                    ...$snapshot,

                    'customer_profile_id'
                        => $customerId,

                    'order_id'
                        => $orderId,

                    'status'
                        => VoucherUsage::STATUS_RESERVED,

                    'is_valid'
                        => true,

                    'used_at'
                        => now(),

                    'metadata'
                        => $metadata,

                    'notes'
                        => $notes,
                ]);

                return $usage

                    ->fresh()

                    ->load([
                        'voucher',
                        'customerProfile',
                        'order',
                    ]);
            }
        );
    }

    /**
     * Mark reserved voucher as used.
     */
    public function useVoucher(
        VoucherUsage|int $usage
    ): VoucherUsage {

        return DB::transaction(
            function () use ($usage) {

                $usage = $usage instanceof VoucherUsage
                    ? $usage->loadMissing(
                        'voucher'
                    )
                    : VoucherUsage::query()
                        ->with('voucher')
                        ->findOrFail($usage);

                if (
                    ! in_array(
                        $usage->status,
                        [
                            VoucherUsage::STATUS_RESERVED,
                            VoucherUsage::STATUS_USED,
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([

                        'voucher' => [
                            'Voucher tidak dapat digunakan.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Reserved → Used
                |--------------------------------------------------------------------------
                */

                if (
                    $usage->status ===
                    VoucherUsage::STATUS_RESERVED
                ) {

                    $usage->markAsUsed();

                    $usage->voucher?->incrementUsage();
                }

                /*
                |--------------------------------------------------------------------------
                | Already Used
                |--------------------------------------------------------------------------
                |
                | Idempotent:
                | Tidak melakukan increment dua kali.
                |
                */

                return $usage

                    ->fresh()

                    ->load([
                        'voucher',
                        'customerProfile',
                        'order',
                    ]);
            }
        );
    }
    /**
     * Cancel voucher usage.
     */
    public function cancelVoucher(
        VoucherUsage|int $usage
    ): VoucherUsage {

        return DB::transaction(
            function () use ($usage) {

                $usage = $usage instanceof VoucherUsage
                    ? $usage
                    : VoucherUsage::query()
                        ->findOrFail($usage);

                if (
                    $usage->status ===
                    VoucherUsage::STATUS_USED
                ) {

                    throw ValidationException::withMessages([

                        'voucher' => [
                            'Voucher yang sudah digunakan tidak dapat dibatalkan.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Idempotent
                |--------------------------------------------------------------------------
                */

                if (
                    $usage->status !==
                    VoucherUsage::STATUS_CANCELLED
                ) {

                    $usage->markAsCancelled();
                }

                return $usage

                    ->fresh()

                    ->load([
                        'voucher',
                        'customerProfile',
                        'order',
                    ]);
            }
        );
    }

    /**
     * Expire voucher usage.
     */
    public function expireVoucher(
        VoucherUsage|int $usage
    ): VoucherUsage {

        return DB::transaction(
            function () use ($usage) {

                $usage = $usage instanceof VoucherUsage
                    ? $usage
                    : VoucherUsage::query()
                        ->findOrFail($usage);

                if (
                    $usage->status ===
                    VoucherUsage::STATUS_USED
                ) {

                    throw ValidationException::withMessages([

                        'voucher' => [
                            'Voucher yang sudah digunakan tidak dapat di-expire.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Idempotent
                |--------------------------------------------------------------------------
                */

                if (
                    $usage->status !==
                    VoucherUsage::STATUS_EXPIRED
                ) {

                    $usage->markAsExpired();
                }

                return $usage

                    ->fresh()

                    ->load([
                        'voucher',
                        'customerProfile',
                        'order',
                    ]);
            }
        );
    }

    /**
     * Validate voucher availability.
     */
    protected function validateVoucherAvailability(
        Voucher $voucher,
        float $subtotal
    ): void {

        if (! $voucher->is_active) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Voucher sedang tidak aktif.',
                ],
            ]);
        }

        if (

            $voucher->start_at !== null

            &&

            now()->lt(
                $voucher->start_at
            )

        ) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Voucher belum dapat digunakan.',
                ],
            ]);
        }

        if (

            $voucher->end_at !== null

            &&

            now()->gt(
                $voucher->end_at
            )

        ) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Voucher telah kedaluwarsa.',
                ],
            ]);
        }

        if (

            $voucher->usage_limit !== null

            &&

            $voucher->used_count >=
            $voucher->usage_limit

        ) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Kuota penggunaan voucher telah habis.',
                ],
            ]);
        }

        if (

            $subtotal <
            (float) $voucher->minimum_purchase

        ) {

            throw ValidationException::withMessages([

                'voucher' => [

                    'Minimal pembelian untuk menggunakan voucher adalah Rp '

                    . number_format(
                        (float) $voucher->minimum_purchase,
                        0,
                        ',',
                        '.'
                    )

                    . '.',
                ],
            ]);
        }
    }

    /**
     * Validate customer voucher usage.
     */
    protected function validateCustomerUsage(
        Voucher $voucher,
        int $customerId
    ): void {

        if (

            $voucher->usage_per_user === null

            ||

            $voucher->usage_per_user <= 0

        ) {

            return;
        }

        $usedCount = VoucherUsage::query()

            ->where(
                'voucher_id',
                $voucher->id
            )

            ->where(
                'customer_profile_id',
                $customerId
            )

            ->used()

            ->count();

        if (

            $usedCount >=
            $voucher->usage_per_user

        ) {

            throw ValidationException::withMessages([

                'voucher' => [
                    'Batas penggunaan voucher telah tercapai.',
                ],
            ]);
        }
    }

    /**
     * Build voucher usage snapshot.
     */
    protected function buildUsageSnapshot(
        Voucher $voucher,
        float $subtotal,
        float $discount
    ): array {

        return [

            'voucher_id'
                => $voucher->id,

            'voucher_code'
                => $voucher->code,

            'voucher_name'
                => $voucher->name,

            'discount_amount'
                => $discount,

            'order_subtotal'
                => $subtotal,

            'order_total'
                => max(
                    0,
                    $subtotal - $discount
                ),
        ];
    }

    /**
     * Validate voucher data.
     */
    protected function validateVoucherData(
        array $data
    ): void {

        if (

            isset(
                $data['start_at'],
                $data['end_at']
            )

            &&

            $data['end_at'] < $data['start_at']

        ) {

            throw ValidationException::withMessages([

                'end_at' => [
                    'Tanggal berakhir harus setelah tanggal mulai.',
                ],
            ]);
        }

        if (

            isset(
                $data['usage_limit'],
                $data['used_count']
            )

            &&

            $data['usage_limit'] !== null

            &&

            $data['usage_limit'] <
            $data['used_count']

        ) {

            throw ValidationException::withMessages([

                'usage_limit' => [
                    'Batas penggunaan tidak boleh lebih kecil dari jumlah penggunaan.',
                ],
            ]);
        }

        if (

            isset(
                $data['discount_value']
            )

            &&

            (float) $data['discount_value'] < 0

        ) {

            throw ValidationException::withMessages([

                'discount_value' => [
                    'Nilai diskon tidak boleh negatif.',
                ],
            ]);
        }

        if (

            isset(
                $data['minimum_purchase']
            )

            &&

            (float) $data['minimum_purchase'] < 0

        ) {

            throw ValidationException::withMessages([

                'minimum_purchase' => [
                    'Minimum pembelian tidak boleh negatif.',
                ],
            ]);
        }
    }
}