<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\PointTransaction;
use App\Models\CustomerProfile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoyaltyPointService
{
    /**
     * Default loyalty relationships.
     */
    protected array $relations = [
        'customerProfile',
        'transactions',
    ];

    /**
     * Default transaction relationships.
     */
    protected array $transactionRelations = [
        'customerProfile',
        'order',
        'approver',
    ];

    /**
     * Get paginated loyalty accounts.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return LoyaltyPoint::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['tier'] ?? null),
                fn ($query) => $query->tier(
                    $filters['tier']
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
                    'published',
                    $filters
                ),
                fn ($query) => $filters['published']
                    ? $query->published()
                    : $query->whereNull(
                        'published_at'
                    )
            )

            ->when(
                array_key_exists(
                    'expired_tier',
                    $filters
                ),
                fn ($query) => $filters['expired_tier']
                    ? $query->expiredTier()
                    : $query
            )

            ->when(
                ($filters['highest_points'] ?? false) === true,
                fn ($query) => $query->highestPoints()
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all loyalty accounts.
     */
    public function all(
        array $filters = []
    ): Collection {

        return LoyaltyPoint::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['tier'] ?? null),
                fn ($query) => $query->tier(
                    $filters['tier']
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
                    'published',
                    $filters
                ),
                fn ($query) => $filters['published']
                    ? $query->published()
                    : $query->whereNull(
                        'published_at'
                    )
            )

            ->when(
                array_key_exists(
                    'expired_tier',
                    $filters
                ),
                fn ($query) => $filters['expired_tier']
                    ? $query->expiredTier()
                    : $query
            )

            ->when(
                ($filters['highest_points'] ?? false) === true,
                fn ($query) => $query->highestPoints()
            )

            ->latest()

            ->get();
    }

    /**
     * Find loyalty account by ID.
     */
    public function find(
        int $id
    ): ?LoyaltyPoint {

        return LoyaltyPoint::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find loyalty account or fail.
     */
    public function findOrFail(
        int $id
    ): LoyaltyPoint {

        return LoyaltyPoint::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Find loyalty account by customer.
     */
    public function findByCustomer(
        CustomerProfile|int $customer
    ): ?LoyaltyPoint {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        return LoyaltyPoint::query()

            ->with($this->relations)

            ->byCustomer($customerId)

            ->first();
    }

    /**
     * Get existing loyalty account
     * or create a new one.
     */
    public function getOrCreate(
        CustomerProfile|int $customer
    ): LoyaltyPoint {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        CustomerProfile::query()
            ->findOrFail($customerId);

        $loyalty = LoyaltyPoint::query()

            ->firstOrCreate(

                [
                    'customer_profile_id'
                        => $customerId,
                ],

                [
                    'current_points'
                        => 0,

                    'available_points'
                        => 0,

                    'pending_points'
                        => 0,

                    'earned_points'
                        => 0,

                    'redeemed_points'
                        => 0,

                    'expired_points'
                        => 0,

                    'lifetime_points'
                        => 0,

                    'lifetime_orders'
                        => 0,

                    'lifetime_spending'
                        => 0,

                    'tier'
                        => LoyaltyPoint::TIER_BRONZE,

                    'tier_upgraded_at'
                        => null,

                    'tier_expires_at'
                        => null,

                    'last_earned_at'
                        => null,

                    'last_redeemed_at'
                        => null,

                    'last_activity_at'
                        => now(),

                    'last_expired_at'
                        => null,

                    'total_expiration_events'
                        => 0,

                    'is_active'
                        => true,

                    'published_at'
                        => now(),

                    'metadata'
                        => null,
                ]
            );

        return $loyalty

            ->fresh()

            ->load($this->relations);
    }
    /**
     * Create loyalty account.
     */
    public function create(
        array $data
    ): LoyaltyPoint {

        return DB::transaction(
            function () use ($data) {

                CustomerProfile::query()
                    ->findOrFail(
                        $data['customer_profile_id']
                    );

                $loyalty = LoyaltyPoint::create([

                    'customer_profile_id'
                        => $data['customer_profile_id'],

                    'current_points'
                        => $data['current_points']
                        ?? 0,

                    'available_points'
                        => $data['available_points']
                        ?? 0,

                    'pending_points'
                        => $data['pending_points']
                        ?? 0,

                    'earned_points'
                        => $data['earned_points']
                        ?? 0,

                    'redeemed_points'
                        => $data['redeemed_points']
                        ?? 0,

                    'expired_points'
                        => $data['expired_points']
                        ?? 0,

                    'lifetime_points'
                        => $data['lifetime_points']
                        ?? 0,

                    'lifetime_orders'
                        => $data['lifetime_orders']
                        ?? 0,

                    'lifetime_spending'
                        => $data['lifetime_spending']
                        ?? 0,

                    'tier'
                        => $data['tier']
                        ?? LoyaltyPoint::TIER_BRONZE,

                    'tier_upgraded_at'
                        => $data['tier_upgraded_at']
                        ?? null,

                    'tier_expires_at'
                        => $data['tier_expires_at']
                        ?? null,

                    'last_earned_at'
                        => $data['last_earned_at']
                        ?? null,

                    'last_redeemed_at'
                        => $data['last_redeemed_at']
                        ?? null,

                    'last_activity_at'
                        => $data['last_activity_at']
                        ?? now(),

                    'last_expired_at'
                        => $data['last_expired_at']
                        ?? null,

                    'total_expiration_events'
                        => $data['total_expiration_events']
                        ?? 0,

                    'is_active'
                        => $data['is_active']
                        ?? true,

                    'published_at'
                        => $data['published_at']
                        ?? now(),

                    'metadata'
                        => $data['metadata']
                        ?? null,
                ]);

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Update loyalty account.
     */
    public function update(
        LoyaltyPoint|int $loyalty,
        array $data
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $loyalty,
                $data
            ) {

                $loyalty = $loyalty instanceof LoyaltyPoint
                    ? $loyalty
                    : $this->findOrFail(
                        $loyalty
                    );

                $loyalty->update([

                    'current_points'
                        => $data['current_points']
                        ?? $loyalty->current_points,

                    'available_points'
                        => $data['available_points']
                        ?? $loyalty->available_points,

                    'pending_points'
                        => $data['pending_points']
                        ?? $loyalty->pending_points,

                    'earned_points'
                        => $data['earned_points']
                        ?? $loyalty->earned_points,

                    'redeemed_points'
                        => $data['redeemed_points']
                        ?? $loyalty->redeemed_points,

                    'expired_points'
                        => $data['expired_points']
                        ?? $loyalty->expired_points,

                    'lifetime_points'
                        => $data['lifetime_points']
                        ?? $loyalty->lifetime_points,

                    'lifetime_orders'
                        => $data['lifetime_orders']
                        ?? $loyalty->lifetime_orders,

                    'lifetime_spending'
                        => $data['lifetime_spending']
                        ?? $loyalty->lifetime_spending,

                    'tier'
                        => $data['tier']
                        ?? $loyalty->tier,

                    'tier_upgraded_at'
                        => array_key_exists(
                            'tier_upgraded_at',
                            $data
                        )
                            ? $data['tier_upgraded_at']
                            : $loyalty->tier_upgraded_at,

                    'tier_expires_at'
                        => array_key_exists(
                            'tier_expires_at',
                            $data
                        )
                            ? $data['tier_expires_at']
                            : $loyalty->tier_expires_at,

                    'last_earned_at'
                        => array_key_exists(
                            'last_earned_at',
                            $data
                        )
                            ? $data['last_earned_at']
                            : $loyalty->last_earned_at,

                    'last_redeemed_at'
                        => array_key_exists(
                            'last_redeemed_at',
                            $data
                        )
                            ? $data['last_redeemed_at']
                            : $loyalty->last_redeemed_at,

                    'last_activity_at'
                        => array_key_exists(
                            'last_activity_at',
                            $data
                        )
                            ? $data['last_activity_at']
                            : $loyalty->last_activity_at,

                    'last_expired_at'
                        => array_key_exists(
                            'last_expired_at',
                            $data
                        )
                            ? $data['last_expired_at']
                            : $loyalty->last_expired_at,

                    'total_expiration_events'
                        => $data['total_expiration_events']
                        ?? $loyalty->total_expiration_events,

                    'is_active'
                        => $data['is_active']
                        ?? $loyalty->is_active,

                    'published_at'
                        => array_key_exists(
                            'published_at',
                            $data
                        )
                            ? $data['published_at']
                            : $loyalty->published_at,

                    'metadata'
                        => array_key_exists(
                            'metadata',
                            $data
                        )
                            ? $data['metadata']
                            : $loyalty->metadata,
                ]);

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Delete loyalty account.
     */
    public function delete(
        LoyaltyPoint|int $loyalty
    ): bool {

        return DB::transaction(
            function () use ($loyalty) {

                $loyalty = $loyalty instanceof LoyaltyPoint
                    ? $loyalty
                    : $this->findOrFail(
                        $loyalty
                    );

                return (bool)
                    $loyalty->delete();
            }
        );
    }

    /**
     * Activate loyalty account.
     */
    public function activate(
        LoyaltyPoint|int $loyalty
    ): LoyaltyPoint {

        return $this->update(
            $loyalty,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate loyalty account.
     */
    public function deactivate(
        LoyaltyPoint|int $loyalty
    ): LoyaltyPoint {

        return $this->update(
            $loyalty,
            [
                'is_active' => false,
            ]
        );
    }

    /**
     * Publish loyalty account.
     */
    public function publish(
        LoyaltyPoint|int $loyalty
    ): LoyaltyPoint {

        return $this->update(
            $loyalty,
            [
                'published_at' => now(),
            ]
        );
    }
    /**
     * Earn loyalty points.
     */
    public function earnPoints(
        CustomerProfile|int $customer,
        int $points,
        ?int $orderId = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $status = PointTransaction::STATUS_COMPLETED,
        ?string $expiredAt = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $orderId,
                $title,
                $description,
                $metadata,
                $referenceType,
                $referenceId,
                $status,
                $expiredAt
            ) {

                $this->validatePointAmount(
                    $points
                );

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $balanceBefore =
                    (int) $loyalty->available_points;

                $loyalty->addPoints(
                    $points
                );

                $this->createTransaction(

                    loyalty: $loyalty,

                    type: PointTransaction::TYPE_EARN,

                    points: $points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    orderId: $orderId,

                    title:
                        $title
                        ?? 'Earn Loyalty Points',

                    description:
                        $description,

                    metadata:
                        $metadata,

                    referenceType:
                        $referenceType,

                    referenceId:
                        $referenceId,

                    status:
                        $status,

                    expiredAt:
                        $expiredAt
                );

                $this->evaluateTier(
                    $loyalty
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemPoints(
        CustomerProfile|int $customer,
        int $points,
        ?int $orderId = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $orderId,
                $title,
                $description,
                $metadata,
                $referenceType,
                $referenceId
            ) {

                $this->validatePointAmount(
                    $points
                );

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $this->validateRedeem(
                    $loyalty,
                    $points
                );

                $balanceBefore =
                    (int) $loyalty->available_points;

                $loyalty->redeemPoints(
                    $points
                );

                $this->createTransaction(

                    loyalty: $loyalty,

                    type: PointTransaction::TYPE_REDEEM,

                    points: -$points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    orderId: $orderId,

                    title:
                        $title
                        ?? 'Redeem Loyalty Points',

                    description:
                        $description,

                    metadata:
                        $metadata,

                    referenceType:
                        $referenceType,

                    referenceId:
                        $referenceId
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Refund redeemed points.
     */
    public function refundPoints(
        CustomerProfile|int $customer,
        int $points,
        ?int $orderId = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $orderId,
                $title,
                $description,
                $metadata,
                $referenceType,
                $referenceId
            ) {

                $this->validatePointAmount(
                    $points
                );

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $balanceBefore =
                    (int) $loyalty->available_points;

                $loyalty->addPoints(
                    $points
                );

                $this->createTransaction(

                    loyalty: $loyalty,

                    type: PointTransaction::TYPE_REFUND,

                    points: $points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    orderId: $orderId,

                    title:
                        $title
                        ?? 'Refund Loyalty Points',

                    description:
                        $description,

                    metadata:
                        $metadata,

                    referenceType:
                        $referenceType,

                    referenceId:
                        $referenceId
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Grant bonus points.
     */
    public function bonusPoints(
        CustomerProfile|int $customer,
        int $points,
        ?int $approvedBy = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $approvedBy,
                $title,
                $description,
                $metadata
            ) {

                $this->validatePointAmount(
                    $points
                );

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $balanceBefore =
                    (int) $loyalty->available_points;

                $loyalty->addPoints(
                    $points
                );

                $this->createTransaction(

                    loyalty: $loyalty,

                    type: PointTransaction::TYPE_BONUS,

                    points: $points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    approvedBy: $approvedBy,

                    approvedAt:
                        $approvedBy
                            ? now()
                            : null,

                    title:
                        $title
                        ?? 'Bonus Loyalty Points',

                    description:
                        $description,

                    metadata:
                        $metadata,

                    status:
                        PointTransaction::STATUS_COMPLETED
                );

                $this->evaluateTier(
                    $loyalty
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Manual point adjustment.
     */
    public function adjustPoints(
        CustomerProfile|int $customer,
        int $points,
        ?int $approvedBy = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $approvedBy,
                $title,
                $description,
                $metadata
            ) {

                if ($points === 0) {

                    throw ValidationException::withMessages([

                        'points' => [
                            'Jumlah poin tidak boleh nol.',
                        ],
                    ]);
                }

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $balanceBefore =
                    (int) $loyalty->available_points;

                if ($points > 0) {

                    $loyalty->addPoints(
                        $points
                    );
                } else {

                    $this->validateRedeem(
                        $loyalty,
                        abs($points)
                    );

                    $loyalty->redeemPoints(
                        abs($points)
                    );
                }

                $this->createTransaction(

                    loyalty: $loyalty,

                    type:
                        PointTransaction::TYPE_ADJUSTMENT,

                    points: $points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    approvedBy: $approvedBy,

                    approvedAt:
                        $approvedBy
                            ? now()
                            : null,

                    title:
                        $title
                        ?? 'Point Adjustment',

                    description:
                        $description,

                    metadata:
                        $metadata,

                    status:
                        PointTransaction::STATUS_COMPLETED
                );

                $this->evaluateTier(
                    $loyalty
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }
    /**
     * Expire loyalty points.
     */
    public function expirePoints(
        CustomerProfile|int $customer,
        int $points,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $customer,
                $points,
                $title,
                $description,
                $metadata
            ) {

                $this->validatePointAmount(
                    $points
                );

                $loyalty = $this->getOrCreate(
                    $customer
                );

                $availablePoints =
                    (int) $loyalty->available_points;

                $points = min(
                    $points,
                    $availablePoints
                );

                if ($points <= 0) {

                    return $loyalty

                        ->fresh()

                        ->load($this->relations);
                }

                $balanceBefore =
                    $availablePoints;

                $loyalty->expirePoints(
                    $points
                );

                $this->createTransaction(

                    loyalty: $loyalty,

                    type: PointTransaction::TYPE_EXPIRE,

                    points: -$points,

                    balanceBefore: $balanceBefore,

                    balanceAfter:
                        (int) $loyalty->fresh()->available_points,

                    title:
                        $title
                        ?? 'Expired Loyalty Points',

                    description:
                        $description,

                    metadata:
                        $metadata
                );

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Find transaction by ID.
     */
    public function findTransaction(
        int $id
    ): ?PointTransaction {

        return PointTransaction::query()

            ->with(
                $this->transactionRelations
            )

            ->find($id);
    }

    /**
     * Find transaction or fail.
     */
    public function findTransactionOrFail(
        int $id
    ): PointTransaction {

        return PointTransaction::query()

            ->with(
                $this->transactionRelations
            )

            ->findOrFail($id);
    }

    /**
     * Get paginated transactions.
     */
    public function paginateTransactions(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return PointTransaction::query()

            ->with(
                $this->transactionRelations
            )

            ->search(
                $filters['search']
                ?? null
            )

            ->forCustomer(
                $filters['customer_profile_id']
                ?? null
            )

            ->type(
                $filters['type']
                ?? null
            )

            ->status(
                $filters['status']
                ?? null
            )

            ->when(
                ($filters['expired'] ?? false)
                === true,
                fn ($query)
                    => $query->expired()
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all transactions.
     */
    public function allTransactions(
        array $filters = []
    ): Collection {

        return PointTransaction::query()

            ->with(
                $this->transactionRelations
            )

            ->search(
                $filters['search']
                ?? null
            )

            ->forCustomer(
                $filters['customer_profile_id']
                ?? null
            )

            ->type(
                $filters['type']
                ?? null
            )

            ->status(
                $filters['status']
                ?? null
            )

            ->when(
                ($filters['expired'] ?? false)
                === true,
                fn ($query)
                    => $query->expired()
            )

            ->latest()

            ->get();
    }

    /**
     * Approve transaction.
     */
    public function approveTransaction(
        PointTransaction|int $transaction,
        int $approvedBy
    ): PointTransaction {

        return DB::transaction(
            function () use (
                $transaction,
                $approvedBy
            ) {

                $transaction =
                    $transaction instanceof PointTransaction
                        ? $transaction
                        : $this->findTransactionOrFail(
                            $transaction
                        );

                if (
                    $transaction->isCompleted()
                ) {

                    return $transaction

                        ->fresh()

                        ->load(
                            $this->transactionRelations
                        );
                }

                if (
                    $transaction->isCancelled()
                ) {

                    throw ValidationException::withMessages([

                        'transaction' => [
                            'Transaksi yang dibatalkan tidak dapat disetujui.',
                        ],
                    ]);
                }

                $transaction->approve(
                    $approvedBy
                );

                return $transaction

                    ->fresh()

                    ->load(
                        $this->transactionRelations
                    );
            }
        );
    }

    /**
     * Cancel transaction.
     */
    public function cancelTransaction(
        PointTransaction|int $transaction
    ): PointTransaction {

        return DB::transaction(
            function () use (
                $transaction
            ) {

                $transaction =
                    $transaction instanceof PointTransaction
                        ? $transaction
                        : $this->findTransactionOrFail(
                            $transaction
                        );

                if (
                    $transaction->isCompleted()
                ) {

                    throw ValidationException::withMessages([

                        'transaction' => [
                            'Transaksi yang sudah selesai tidak dapat dibatalkan.',
                        ],
                    ]);
                }

                if (
                    ! $transaction->isCancelled()
                ) {

                    $transaction->cancel();
                }

                return $transaction

                    ->fresh()

                    ->load(
                        $this->transactionRelations
                    );
            }
        );
    }
    /**
     * Evaluate customer tier.
     */
    public function evaluateTier(
        LoyaltyPoint|int $loyalty
    ): LoyaltyPoint {

        return DB::transaction(
            function () use ($loyalty) {

                $loyalty = $loyalty instanceof LoyaltyPoint
                    ? $loyalty
                    : $this->findOrFail(
                        $loyalty
                    );

                $newTier = match (true) {

                    $loyalty->lifetime_spending >= 10000000
                        => LoyaltyPoint::TIER_PLATINUM,

                    $loyalty->lifetime_spending >= 5000000
                        => LoyaltyPoint::TIER_GOLD,

                    $loyalty->lifetime_spending >= 1000000
                        => LoyaltyPoint::TIER_SILVER,

                    default
                        => LoyaltyPoint::TIER_BRONZE,
                };

                if (
                    $newTier !== $loyalty->tier
                ) {

                    $loyalty->upgradeTier(
                        $newTier
                    );
                }

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Upgrade tier manually.
     */
    public function upgradeTier(
        LoyaltyPoint|int $loyalty,
        string $tier,
        ?string $expiresAt = null
    ): LoyaltyPoint {

        return DB::transaction(
            function () use (
                $loyalty,
                $tier,
                $expiresAt
            ) {

                $loyalty = $loyalty instanceof LoyaltyPoint
                    ? $loyalty
                    : $this->findOrFail(
                        $loyalty
                    );

                $allowedTiers = [

                    LoyaltyPoint::TIER_BRONZE,

                    LoyaltyPoint::TIER_SILVER,

                    LoyaltyPoint::TIER_GOLD,

                    LoyaltyPoint::TIER_PLATINUM,
                ];

                if (
                    ! in_array(
                        $tier,
                        $allowedTiers,
                        true
                    )
                ) {

                    throw ValidationException::withMessages([

                        'tier' => [
                            'Tier loyalty tidak valid.',
                        ],
                    ]);
                }

                $loyalty->update([

                    'tier'
                        => $tier,

                    'tier_upgraded_at'
                        => now(),

                    'tier_expires_at'
                        => $expiresAt,
                ]);

                return $loyalty

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Downgrade expired tiers.
     */
    public function downgradeExpiredTiers(): int
    {
        return LoyaltyPoint::query()

            ->expiredTier()

            ->update([

                'tier'
                    => LoyaltyPoint::TIER_BRONZE,

                'tier_expires_at'
                    => null,
            ]);
    }

    /**
     * Create point transaction.
     */
    protected function createTransaction(
        LoyaltyPoint $loyalty,
        string $type,
        int $points,
        int $balanceBefore,
        int $balanceAfter,
        ?int $orderId = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $approvedBy = null,
        $approvedAt = null,
        string $status = PointTransaction::STATUS_COMPLETED,
        $expiredAt = null
    ): PointTransaction {

        return PointTransaction::create([

            'customer_profile_id'
                => $loyalty->customer_profile_id,

            'order_id'
                => $orderId,

            'transaction_number'
                => $this->generateTransactionNumber(),

            'type'
                => $type,

            'points'
                => $points,

            'balance_before'
                => $balanceBefore,

            'balance_after'
                => $balanceAfter,

            'reference_type'
                => $referenceType,

            'reference_id'
                => $referenceId,

            'title'
                => $title
                ?? ucfirst($type),

            'description'
                => $description,

            'expired_at'
                => $expiredAt,

            'approved_by'
                => $approvedBy,

            'approved_at'
                => $approvedAt,

            'status'
                => $status,

            'metadata'
                => $metadata,

            'transaction_at'
                => now(),
        ]);
    }

    /**
     * Generate transaction number.
     */
    protected function generateTransactionNumber(): string
    {
        do {

            $number =
                'LP-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -6
                    )
                );

        } while (

            PointTransaction::query()

                ->where(
                    'transaction_number',
                    $number
                )

                ->exists()
        );

        return $number;
    }

    /**
     * Validate redeem request.
     */
    protected function validateRedeem(
        LoyaltyPoint $loyalty,
        int $points
    ): void {

        if (
            ! $loyalty->is_active
        ) {

            throw ValidationException::withMessages([

                'loyalty' => [
                    'Akun loyalty tidak aktif.',
                ],
            ]);
        }

        if (
            ! $loyalty->hasAvailablePoints(
                $points
            )
        ) {

            throw ValidationException::withMessages([

                'points' => [
                    'Poin tidak mencukupi.',
                ],
            ]);
        }
    }

    /**
     * Validate point amount.
     */
    protected function validatePointAmount(
        int $points
    ): void {

        if ($points <= 0) {

            throw ValidationException::withMessages([

                'points' => [
                    'Jumlah poin harus lebih dari nol.',
                ],
            ]);
        }
    }
}