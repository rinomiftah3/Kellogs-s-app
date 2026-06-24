<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use App\Models\OrderStatusLog;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'customerProfile',
        'shippingAddress',
        'items',
        'histories',
        'statusLogs',
        'payment',
        'shipment',
        'voucherUsage',
    ];

    /**
     * Get paginated orders.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Order::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                filled($filters['payment_status'] ?? null),
                fn ($query) => $query->where(
                    'payment_status',
                    $filters['payment_status']
                )
            )

            ->when(
                filled($filters['fulfillment_status'] ?? null),
                fn ($query) => $query->where(
                    'fulfillment_status',
                    $filters['fulfillment_status']
                )
            )

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->where(
                    'customer_profile_id',
                    $filters['customer_profile_id']
                )
            )

            ->latestFirst()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all orders.
     */
    public function all(
        array $filters = []
    ): Collection {

        return Order::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                filled($filters['payment_status'] ?? null),
                fn ($query) => $query->where(
                    'payment_status',
                    $filters['payment_status']
                )
            )

            ->when(
                filled($filters['fulfillment_status'] ?? null),
                fn ($query) => $query->where(
                    'fulfillment_status',
                    $filters['fulfillment_status']
                )
            )

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->where(
                    'customer_profile_id',
                    $filters['customer_profile_id']
                )
            )

            ->latestFirst()

            ->get();
    }

    /**
     * Find order by ID.
     */
    public function find(
        int $id
    ): ?Order {

        return Order::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find order or fail.
     */
    public function findOrFail(
        int $id
    ): Order {

        return Order::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Find order by order number.
     */
    public function findByOrderNumber(
        string $orderNumber
    ): ?Order {

        return Order::query()

            ->with($this->relations)

            ->where(
                'order_number',
                trim($orderNumber)
            )

            ->first();
    }
    /**
     * Create order.
     */
    public function create(
        array $data
    ): Order {

        return DB::transaction(
            function () use ($data) {

                $customer = CustomerProfile::query()
                    ->findOrFail(
                        $data['customer_profile_id']
                    );

                $address = CustomerAddress::query()
                    ->findOrFail(
                        $data['shipping_address_id']
                    );

                $customerSnapshot =
                    $this->buildCustomerSnapshot(
                        $customer
                    );

                $shippingSnapshot =
                    $this->buildShippingSnapshot(
                        $address
                    );

                $order = Order::create([

                    'customer_profile_id'
                        => $customer->id,

                    'shipping_address_id'
                        => $address->id,

                    'order_number'
                        => $data['order_number']
                        ?? $this->generateOrderNumber(),

                    'status'
                        => $data['status']
                        ?? Order::STATUS_PENDING,

                    'payment_status'
                        => $data['payment_status']
                        ?? Order::PAYMENT_PENDING,

                    'fulfillment_status'
                        => $data['fulfillment_status']
                        ?? Order::FULFILLMENT_PENDING,

                    ...$customerSnapshot,

                    ...$shippingSnapshot,

                    'subtotal'
                        => $data['subtotal']
                        ?? 0,

                    'shipping_cost'
                        => $data['shipping_cost']
                        ?? 0,

                    'discount_amount'
                        => $data['discount_amount']
                        ?? 0,

                    'tax_amount'
                        => $data['tax_amount']
                        ?? 0,

                    'grand_total'
                        => $data['grand_total']
                        ?? 0,

                    'voucher_code'
                        => $data['voucher_code']
                        ?? null,

                    'voucher_discount'
                        => $data['voucher_discount']
                        ?? 0,

                    'courier_code'
                        => $data['courier_code']
                        ?? null,

                    'courier_service'
                        => $data['courier_service']
                        ?? null,

                    'tracking_number'
                        => $data['tracking_number']
                        ?? null,

                    'total_weight'
                        => $data['total_weight']
                        ?? 0,

                    'customer_notes'
                        => $data['customer_notes']
                        ?? null,

                    'admin_notes'
                        => $data['admin_notes']
                        ?? null,

                    'ordered_at'
                        => $data['ordered_at']
                        ?? now(),

                    'paid_at'
                        => $data['paid_at']
                        ?? null,

                    'shipped_at'
                        => $data['shipped_at']
                        ?? null,

                    'completed_at'
                        => $data['completed_at']
                        ?? null,

                    'cancelled_at'
                        => $data['cancelled_at']
                        ?? null,

                    'metadata'
                        => $data['metadata']
                        ?? null,
                ]);

                return $order
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Update order.
     */
    public function update(
        Order|int $order,
        array $data
    ): Order {

        return DB::transaction(
            function () use (
                $order,
                $data
            ) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                $order->update([

                    'status'
                        => $data['status']
                        ?? $order->status,

                    'payment_status'
                        => $data['payment_status']
                        ?? $order->payment_status,

                    'fulfillment_status'
                        => $data['fulfillment_status']
                        ?? $order->fulfillment_status,

                    'subtotal'
                        => $data['subtotal']
                        ?? $order->subtotal,

                    'shipping_cost'
                        => $data['shipping_cost']
                        ?? $order->shipping_cost,

                    'discount_amount'
                        => $data['discount_amount']
                        ?? $order->discount_amount,

                    'tax_amount'
                        => $data['tax_amount']
                        ?? $order->tax_amount,

                    'grand_total'
                        => $data['grand_total']
                        ?? $order->grand_total,

                    'voucher_code'
                        => array_key_exists(
                            'voucher_code',
                            $data
                        )
                            ? $data['voucher_code']
                            : $order->voucher_code,

                    'voucher_discount'
                        => $data['voucher_discount']
                        ?? $order->voucher_discount,

                    'courier_code'
                        => array_key_exists(
                            'courier_code',
                            $data
                        )
                            ? $data['courier_code']
                            : $order->courier_code,

                    'courier_service'
                        => array_key_exists(
                            'courier_service',
                            $data
                        )
                            ? $data['courier_service']
                            : $order->courier_service,

                    'tracking_number'
                        => array_key_exists(
                            'tracking_number',
                            $data
                        )
                            ? $data['tracking_number']
                            : $order->tracking_number,

                    'customer_notes'
                        => array_key_exists(
                            'customer_notes',
                            $data
                        )
                            ? $data['customer_notes']
                            : $order->customer_notes,

                    'admin_notes'
                        => array_key_exists(
                            'admin_notes',
                            $data
                        )
                            ? $data['admin_notes']
                            : $order->admin_notes,

                    'metadata'
                        => array_key_exists(
                            'metadata',
                            $data
                        )
                            ? $data['metadata']
                            : $order->metadata,
                ]);

                return $order
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Delete order.
     */
    public function delete(
        Order|int $order
    ): bool {

        return DB::transaction(
            function () use ($order) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                return (bool)
                    $order->delete();
            }
        );
    }

    /**
     * Generate order number.
     */
    protected function generateOrderNumber(): string
    {
        do {

            $orderNumber =
                'ORD-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -6
                    )
                );

        } while (

            Order::query()
                ->where(
                    'order_number',
                    $orderNumber
                )
                ->exists()
        );

        return $orderNumber;
    }

    /**
     * Build customer snapshot.
     */
    protected function buildCustomerSnapshot(
        CustomerProfile $customer
    ): array {

        return [

            'customer_name'
                => $customer->full_name,

            'customer_email'
                => $customer->email,

            'customer_phone'
                => $customer->phone,
        ];
    }

    /**
     * Build shipping snapshot.
     */
    protected function buildShippingSnapshot(
        CustomerAddress $address
    ): array {

        return [

            'recipient_name'
                => $address->recipient_name,

            'recipient_phone'
                => $address->recipient_phone,

            'shipping_address'
                => $address->address,

            'province'
                => $address->province,

            'city'
                => $address->city,

            'district'
                => $address->district,

            'postal_code'
                => $address->postal_code,
        ];
    }
    /**
     * Create order items.
     */
    public function createItems(
        Order|int $order,
        array $items
    ): Order {

        return DB::transaction(
            function () use (
                $order,
                $items
            ) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                foreach ($items as $item) {

                    OrderItem::create([

                        'order_id'
                            => $order->id,

                        'product_sku_id'
                            => $item['product_sku_id'],

                        'product_id'
                            => $item['product_id'],

                        'category_id'
                            => $item['category_id']
                            ?? null,

                        'product_name'
                            => $item['product_name'],

                        'product_slug'
                            => $item['product_slug']
                            ?? null,

                        'sku'
                            => $item['sku'],

                        'barcode'
                            => $item['barcode']
                            ?? null,

                        'variant_name'
                            => $item['variant_name']
                            ?? null,

                        'thumbnail'
                            => $item['thumbnail']
                            ?? null,

                        'weight'
                            => $item['weight']
                            ?? 0,

                        'unit_price'
                            => $item['unit_price'],

                        'discount_amount'
                            => $item['discount_amount']
                            ?? 0,

                        'final_price'
                            => $item['final_price'],

                        'quantity'
                            => $item['quantity'],

                        'subtotal'
                            => $item['subtotal'],

                        'promotion_name'
                            => $item['promotion_name']
                            ?? null,

                        'promotion_code'
                            => $item['promotion_code']
                            ?? null,

                        'metadata'
                            => $item['metadata']
                            ?? null,
                    ]);
                }

                return $this->recalculateTotals(
                    $order
                );
            }
        );
    }

    /**
     * Recalculate order totals.
     */
    public function recalculateTotals(
        Order|int $order
    ): Order {

        return DB::transaction(
            function () use ($order) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                $subtotal =
                    $this->calculateSubtotal(
                        $order
                    );

                $totalWeight =
                    $this->calculateTotalWeight(
                        $order
                    );

                $shippingCost =
                    (float) $order->shipping_cost;

                $discount =
                    (float) $order->discount_amount;

                $tax =
                    (float) $order->tax_amount;

                $grandTotal = max(
                    0,
                    (
                        $subtotal
                        + $shippingCost
                        + $tax
                    )
                    - $discount
                );

                $order->update([

                    'subtotal'
                        => $subtotal,

                    'total_weight'
                        => $totalWeight,

                    'grand_total'
                        => $grandTotal,
                ]);

                return $order
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Calculate subtotal.
     */
    protected function calculateSubtotal(
        Order $order
    ): float {

        return round(

            (float) $order->items()
                ->sum('subtotal'),

            2
        );
    }

    /**
     * Calculate total weight.
     */
    protected function calculateTotalWeight(
        Order $order
    ): int {

        return (int)

            $order->items()

                ->get()

                ->sum(
                    fn (OrderItem $item)
                        => (
                            (int) $item->weight
                            * (int) $item->quantity
                        )
                );
    }
    /**
     * Update order status.
     */
    public function updateStatus(
        Order|int $order,
        string $status
    ): Order {

        return DB::transaction(
            function () use (
                $order,
                $status
            ) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                $this->validateStatusTransition(
                    $order,
                    $status
                );

                $oldStatus = $order->status;

                $attributes = [
                    'status' => $status,
                ];

                if (
                    $status ===
                    Order::STATUS_PROCESSING
                ) {

                    $attributes[
                        'fulfillment_status'
                    ] = Order::FULFILLMENT_PACKED;
                }

                if (
                    $status ===
                    Order::STATUS_SHIPPED
                ) {

                    $attributes['shipped_at']
                        = now();

                    $attributes[
                        'fulfillment_status'
                    ] = Order::FULFILLMENT_SHIPPED;
                }

                if (
                    $status ===
                    Order::STATUS_COMPLETED
                ) {

                    $attributes['completed_at']
                        = now();

                    $attributes[
                        'fulfillment_status'
                    ] = Order::FULFILLMENT_DELIVERED;
                }

                if (
                    $status ===
                    Order::STATUS_CANCELLED
                ) {

                    $attributes['cancelled_at']
                        = now();
                }

                $order->update($attributes);

                $this->createHistory(
                    order: $order,
                    action: 'status_changed',
                    oldStatus: $oldStatus,
                    newStatus: $status,
                    description: "Status changed from {$oldStatus} to {$status}"
                );

                $this->createStatusLog(
                    order: $order,
                    fromStatus: $oldStatus,
                    toStatus: $status
                );
                return $order
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(
        Order|int $order
    ): Order {

        return DB::transaction(
            function () use ($order) {

                $order = $order instanceof Order
                    ? $order
                    : $this->findOrFail(
                        $order
                    );

                if (
                    $order->payment_status ===
                    Order::PAYMENT_PAID
                ) {

                    return $order
                        ->fresh()
                        ->load($this->relations);
                }

                $order->update([

                    'payment_status'
                        => Order::PAYMENT_PAID,

                    'status'
                        => Order::STATUS_CONFIRMED,

                    'paid_at'
                        => now(),
                ]);

                return $order
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(
        Order|int $order
    ): Order {

        return $this->updateStatus(
            $order,
            Order::STATUS_PROCESSING
        );
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(
        Order|int $order
    ): Order {

        return $this->updateStatus(
            $order,
            Order::STATUS_SHIPPED
        );
    }

    /**
     * Mark order as completed.
     */
    public function markAsCompleted(
        Order|int $order
    ): Order {

        return $this->updateStatus(
            $order,
            Order::STATUS_COMPLETED
        );
    }

    /**
     * Cancel order.
     */
    public function cancel(
        Order|int $order
    ): Order {

        $order = $order instanceof Order
            ? $order
            : $this->findOrFail(
                $order
            );

        if (
            $order->payment_status ===
            Order::PAYMENT_PAID
        ) {

            throw ValidationException::withMessages([

                'order' => [
                    'Pesanan yang sudah dibayar tidak dapat dibatalkan.',
                ],
            ]);
        }

        return $this->updateStatus(
            $order,
            Order::STATUS_CANCELLED
        );
    }

    /**
     * Validate status transition.
     */
    protected function validateStatusTransition(
        Order $order,
        string $newStatus
    ): void {

        $allowedTransitions = [

            Order::STATUS_PENDING => [

                Order::STATUS_CONFIRMED,
                Order::STATUS_CANCELLED,
            ],

            Order::STATUS_CONFIRMED => [

                Order::STATUS_PROCESSING,
                Order::STATUS_CANCELLED,
            ],

            Order::STATUS_PROCESSING => [

                Order::STATUS_SHIPPED,
                Order::STATUS_CANCELLED,
            ],

            Order::STATUS_SHIPPED => [

                Order::STATUS_COMPLETED,
            ],

            Order::STATUS_COMPLETED => [],

            Order::STATUS_CANCELLED => [],
        ];

        $currentStatus =
            $order->status;

        if (
            $currentStatus ===
            $newStatus
        ) {

            return;
        }

        $allowed =
            $allowedTransitions[
                $currentStatus
            ] ?? [];

        if (
            ! in_array(
                $newStatus,
                $allowed,
                true
            )
        ) {

            throw ValidationException::withMessages([

                'status' => [

                    sprintf(
                        'Perubahan status dari "%s" ke "%s" tidak diperbolehkan.',
                        $currentStatus,
                        $newStatus
                    ),
                ],
            ]);
        }
    }
    /**
     * Create order history.
     */
    public function createHistory(
        Order|int $order,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?int $userId = null,
        ?string $description = null,
        ?string $notes = null,
        string $source = OrderHistory::SOURCE_SYSTEM,
        ?array $metadata = null
    ): OrderHistory {

        $order = $order instanceof Order
            ? $order
            : $this->findOrFail(
                $order
            );

        return OrderHistory::create([

            'order_id'
                => $order->id,

            'user_id'
                => $userId,

            'action'
                => $action,

            'old_status'
                => $oldStatus,

            'new_status'
                => $newStatus,

            'description'
                => $description,

            'notes'
                => $notes,

            'source'
                => $source,

            'metadata'
                => $metadata,
        ]);
    }

    /**
     * Create order status log.
     */
    public function createStatusLog(
        Order|int $order,
        ?string $fromStatus,
        string $toStatus,
        ?int $userId = null,
        string $source = OrderStatusLog::SOURCE_SYSTEM,
        ?string $reason = null,
        ?string $notes = null,
        ?array $metadata = null
    ): OrderStatusLog {

        $order = $order instanceof Order
            ? $order
            : $this->findOrFail(
                $order
            );

        $latestLog = OrderStatusLog::query()

            ->where(
                'order_id',
                $order->id
            )

            ->latest('changed_at')

            ->first();

        $durationSeconds = null;

        if (
            $latestLog !== null
            &&
            $latestLog->changed_at !== null
        ) {

            $durationSeconds =
                $latestLog
                    ->changed_at
                    ->diffInSeconds(
                        now()
                    );
        }

        return OrderStatusLog::create([

            'order_id'
                => $order->id,

            'user_id'
                => $userId,

            'from_status'
                => $fromStatus,

            'to_status'
                => $toStatus,

            'changed_at'
                => now(),

            'duration_seconds'
                => $durationSeconds,

            'source'
                => $source,

            'reason'
                => $reason,

            'notes'
                => $notes,

            'metadata'
                => $metadata,
        ]);
    }
}