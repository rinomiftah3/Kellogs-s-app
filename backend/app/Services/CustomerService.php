<?php

namespace App\Services;

use App\Models\CustomerProfile;
use App\Models\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'user',
        'addresses',
        'loyaltyPoint',
    ];

    /**
     * Get paginated customers.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return CustomerProfile::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['membership_level'] ?? null),
                fn ($query) => $query->membership(
                    $filters['membership_level']
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

            ->when(
                array_key_exists(
                    'has_orders',
                    $filters
                ),
                fn ($query) => $filters['has_orders']
                    ? $query->hasOrders()
                    : $query
            )

            ->when(
                array_key_exists(
                    'email_subscribed',
                    $filters
                ),
                fn ($query) => $filters['email_subscribed']
                    ? $query->emailSubscribed()
                    : $query
            )

            ->when(
                array_key_exists(
                    'sms_subscribed',
                    $filters
                ),
                fn ($query) => $filters['sms_subscribed']
                    ? $query->smsSubscribed()
                    : $query
            )

            ->when(
                array_key_exists(
                    'push_subscribed',
                    $filters
                ),
                fn ($query) => $filters['push_subscribed']
                    ? $query->pushSubscribed()
                    : $query
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all customers.
     */
    public function all(
        array $filters = []
    ): Collection {

        return CustomerProfile::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['membership_level'] ?? null),
                fn ($query) => $query->membership(
                    $filters['membership_level']
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

            ->when(
                array_key_exists(
                    'has_orders',
                    $filters
                ),
                fn ($query) => $filters['has_orders']
                    ? $query->hasOrders()
                    : $query
            )

            ->when(
                array_key_exists(
                    'email_subscribed',
                    $filters
                ),
                fn ($query) => $filters['email_subscribed']
                    ? $query->emailSubscribed()
                    : $query
            )

            ->when(
                array_key_exists(
                    'sms_subscribed',
                    $filters
                ),
                fn ($query) => $filters['sms_subscribed']
                    ? $query->smsSubscribed()
                    : $query
            )

            ->when(
                array_key_exists(
                    'push_subscribed',
                    $filters
                ),
                fn ($query) => $filters['push_subscribed']
                    ? $query->pushSubscribed()
                    : $query
            )

            ->latest()

            ->get();
    }

    /**
     * Find customer.
     *
     * Support:
     * - ID
     * - Customer Code
     */
    public function find(
        int|string $customer
    ): ?CustomerProfile {

        return CustomerProfile::query()

            ->with($this->relations)

            ->where(function ($query) use ($customer) {

                $query

                    ->where('id', $customer)

                    ->orWhere(
                        'customer_code',
                        $customer
                    );
            })

            ->first();
    }

    /**
     * Find customer or fail.
     *
     * Support:
     * - CustomerProfile
     * - ID
     * - Customer Code
     */
    public function findOrFail(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        if ($customer instanceof CustomerProfile) {

            return $customer->load(
                $this->relations
            );
        }

        return CustomerProfile::query()

            ->with($this->relations)

            ->where(function ($query) use ($customer) {

                $query

                    ->where('id', $customer)

                    ->orWhere(
                        'customer_code',
                        $customer
                    );
            })

            ->firstOrFail();
    }

    /**
     * Find customer by user.
     */
    public function findByUser(
        User|int $user
    ): ?CustomerProfile {

        $userId = $user instanceof User
            ? $user->id
            : $user;

        return CustomerProfile::query()

            ->with($this->relations)

            ->where(
                'user_id',
                $userId
            )

            ->first();
    }
    /**
     * Create customer profile.
     */
    public function create(
        array $data
    ): CustomerProfile {

        return DB::transaction(
            function () use ($data) {

                /*
                |--------------------------------------------------------------------------
                | Ensure User Exists
                |--------------------------------------------------------------------------
                */

                User::query()
                    ->findOrFail(
                        $data['user_id']
                    );

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Profile
                |--------------------------------------------------------------------------
                */

                $exists = CustomerProfile::query()

                    ->where(
                        'user_id',
                        $data['user_id']
                    )

                    ->exists();

                if ($exists) {

                    throw new \RuntimeException(
                        'User sudah memiliki profil pelanggan.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create Profile
                |--------------------------------------------------------------------------
                */

                $customer = CustomerProfile::create([

                    'user_id'
                        => $data['user_id'],

                    'customer_code'
                        => $this->generateCustomerCode(),

                    'full_name'
                        => trim(
                            $data['full_name']
                        ),

                    'phone'
                        => $data['phone']
                        ?? null,

                    'gender'
                        => $data['gender']
                        ?? null,

                    'birth_date'
                        => $data['birth_date']
                        ?? null,

                    'avatar'
                        => $data['avatar']
                        ?? null,

                    'bio'
                        => $data['bio']
                        ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Membership
                    |--------------------------------------------------------------------------
                    |
                    | Semua customer baru dimulai
                    | dari level REGULAR.
                    |
                    */

                    'membership_level'
                        => CustomerProfile::LEVEL_REGULAR,

                    /*
                    |--------------------------------------------------------------------------
                    | Statistics
                    |--------------------------------------------------------------------------
                    */

                    'total_points'
                        => 0,

                    'total_spent'
                        => 0,

                    'total_orders'
                        => 0,

                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */

                    'is_active'
                        => $data['is_active']
                        ?? true,

                    'last_order_at'
                        => null,

                    /*
                    |--------------------------------------------------------------------------
                    | Marketing Preferences
                    |--------------------------------------------------------------------------
                    */

                    'email_subscribed'
                        => $data['email_subscribed']
                        ?? true,

                    'sms_subscribed'
                        => $data['sms_subscribed']
                        ?? false,

                    'push_subscribed'
                        => $data['push_subscribed']
                        ?? true,
                ]);

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Update customer profile.
     */
    public function update(
        CustomerProfile|int|string $customer,
        array $data
    ): CustomerProfile {

        return DB::transaction(
            function () use (
                $customer,
                $data
            ) {

                $customer = $this->findOrFail(
                    $customer
                );

                /*
                |--------------------------------------------------------------------------
                | Validate User Change
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['user_id'])
                    &&
                    $data['user_id']
                    != $customer->user_id
                ) {

                    User::query()
                        ->findOrFail(
                            $data['user_id']
                        );

                    $exists = CustomerProfile::query()

                        ->where(
                            'user_id',
                            $data['user_id']
                        )

                        ->where(
                            'id',
                            '!=',
                            $customer->id
                        )

                        ->exists();

                    if ($exists) {

                        throw new \RuntimeException(
                            'User sudah memiliki profil pelanggan.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Update Profile
                |--------------------------------------------------------------------------
                */

                $customer->update([

                    'user_id'
                        => $data['user_id']
                        ?? $customer->user_id,

                    'full_name'
                        => $data['full_name']
                        ?? $customer->full_name,

                    'phone'
                        => $data['phone']
                        ?? $customer->phone,

                    'gender'
                        => $data['gender']
                        ?? $customer->gender,

                    'birth_date'
                        => $data['birth_date']
                        ?? $customer->birth_date,

                    'avatar'
                        => array_key_exists(
                            'avatar',
                            $data
                        )
                            ? $data['avatar']
                            : $customer->avatar,

                    'bio'
                        => array_key_exists(
                            'bio',
                            $data
                        )
                            ? $data['bio']
                            : $customer->bio,

                    /*
                    |--------------------------------------------------------------------------
                    | Marketing Preferences
                    |--------------------------------------------------------------------------
                    */

                    'email_subscribed'
                        => array_key_exists(
                            'email_subscribed',
                            $data
                        )
                            ? $data['email_subscribed']
                            : $customer->email_subscribed,

                    'sms_subscribed'
                        => array_key_exists(
                            'sms_subscribed',
                            $data
                        )
                            ? $data['sms_subscribed']
                            : $customer->sms_subscribed,

                    'push_subscribed'
                        => array_key_exists(
                            'push_subscribed',
                            $data
                        )
                            ? $data['push_subscribed']
                            : $customer->push_subscribed,

                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */

                    'is_active'
                        => array_key_exists(
                            'is_active',
                            $data
                        )
                            ? $data['is_active']
                            : $customer->is_active,
                ]);

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Delete customer profile.
     */
    public function delete(
        CustomerProfile|int|string $customer
    ): bool {

        return DB::transaction(
            function () use ($customer) {

                $customer = $this->findOrFail(
                    $customer
                );

                /*
                |--------------------------------------------------------------------------
                | Soft Delete Friendly
                |--------------------------------------------------------------------------
                */

                return (bool)
                    $customer->delete();
            }
        );
    }

    /**
     * Activate customer.
     */
    public function activate(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate customer.
     */
    public function deactivate(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'is_active' => false,
            ]
        );
    }
    /**
     * Increase customer points.
     */
    public function increasePoints(
        CustomerProfile|int|string $customer,
        int $points
    ): CustomerProfile {

        return DB::transaction(
            function () use (
                $customer,
                $points
            ) {

                $customer = $this->findOrFail(
                    $customer
                );

                /*
                |--------------------------------------------------------------------------
                | Invalid Points
                |--------------------------------------------------------------------------
                */

                if ($points <= 0) {

                    return $customer;
                }

                /*
                |--------------------------------------------------------------------------
                | Increase Points
                |--------------------------------------------------------------------------
                */

                $customer->increment(
                    'total_points',
                    $points
                );

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Increase customer order statistics.
     */
    public function increaseOrderCount(
        CustomerProfile|int|string $customer,
        float $totalSpent = 0
    ): CustomerProfile {

        return DB::transaction(
            function () use (
                $customer,
                $totalSpent
            ) {

                $customer = $this->findOrFail(
                    $customer
                );

                /*
                |--------------------------------------------------------------------------
                | Increase Total Orders
                |--------------------------------------------------------------------------
                */

                $customer->increment(
                    'total_orders'
                );

                /*
                |--------------------------------------------------------------------------
                | Increase Total Spent
                |--------------------------------------------------------------------------
                */

                if ($totalSpent > 0) {

                    $customer->increment(
                        'total_spent',
                        $totalSpent
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Update Last Order Timestamp
                |--------------------------------------------------------------------------
                */

                $customer->update([
                    'last_order_at' => now(),
                ]);

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Update last order timestamp.
     */
    public function updateLastOrder(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return DB::transaction(
            function () use ($customer) {

                $customer = $this->findOrFail(
                    $customer
                );

                $customer->update([
                    'last_order_at' => now(),
                ]);

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Subscribe email notification.
     */
    public function subscribeEmail(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'email_subscribed' => true,
            ]
        );
    }

    /**
     * Unsubscribe email notification.
     */
    public function unsubscribeEmail(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'email_subscribed' => false,
            ]
        );
    }

    /**
     * Subscribe SMS notification.
     */
    public function subscribeSms(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'sms_subscribed' => true,
            ]
        );
    }

    /**
     * Unsubscribe SMS notification.
     */
    public function unsubscribeSms(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'sms_subscribed' => false,
            ]
        );
    }

    /**
     * Subscribe push notification.
     */
    public function subscribePush(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'push_subscribed' => true,
            ]
        );
    }

    /**
     * Unsubscribe push notification.
     */
    public function unsubscribePush(
        CustomerProfile|int|string $customer
    ): CustomerProfile {

        return $this->update(
            $customer,
            [
                'push_subscribed' => false,
            ]
        );
    }

    /**
     * Change customer membership.
     */
    public function changeMembership(
        CustomerProfile|int|string $customer,
        string $membershipLevel
    ): CustomerProfile {

        $allowedLevels = [

            CustomerProfile::LEVEL_REGULAR,

            CustomerProfile::LEVEL_SILVER,

            CustomerProfile::LEVEL_GOLD,

            CustomerProfile::LEVEL_PLATINUM,
        ];

        if (
            ! in_array(
                $membershipLevel,
                $allowedLevels,
                true
            )
        ) {

            throw new \RuntimeException(
                'Level membership tidak valid.'
            );
        }

        return DB::transaction(
            function () use (
                $customer,
                $membershipLevel
            ) {

                $customer = $this->findOrFail(
                    $customer
                );

                $customer->update([
                    'membership_level'
                        => $membershipLevel,
                ]);

                return $customer

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Generate unique customer code.
     */
    protected function generateCustomerCode(): string
    {
        do {

            $code = 'CUS-'
                . str_pad(
                    (string) random_int(
                        1,
                        99999
                    ),
                    5,
                    '0',
                    STR_PAD_LEFT
                );

        } while (

            CustomerProfile::query()

                ->where(
                    'customer_code',
                    $code
                )

                ->exists()
        );

        return $code;
    }
}