<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

use Illuminate\Validation\ValidationException;

use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

use RuntimeException;

class PaymentMethodService
{
    /**
     * Available gateways.
     */
    protected array $gateways = [

        'midtrans',
    ];

    /**
     * Available payment methods.
     */
    protected array $methods = [

        'midtrans' => [

            /*
            |--------------------------------------------------------------------------
            | Credit Card
            |--------------------------------------------------------------------------
            */

            'credit_card',

            /*
            |--------------------------------------------------------------------------
            | Virtual Account
            |--------------------------------------------------------------------------
            */

            'bca_va',
            'bni_va',
            'bri_va',
            'permata_va',

            /*
            |--------------------------------------------------------------------------
            | Bank Transfer
            |--------------------------------------------------------------------------
            */

            'echannel',

            /*
            |--------------------------------------------------------------------------
            | E-Wallet
            |--------------------------------------------------------------------------
            */

            'gopay',
            'qris',
            'shopeepay',

            /*
            |--------------------------------------------------------------------------
            | Convenience Store
            |--------------------------------------------------------------------------
            */

            'cstore',
        ],
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $serverKey = config(
            'services.midtrans.server_key'
        );

        $clientKey = config(
            'services.midtrans.client_key'
        );

        if (blank($serverKey)) {

            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.'
            );
        }

        if (blank($clientKey)) {

            throw new RuntimeException(
                'MIDTRANS_CLIENT_KEY belum dikonfigurasi.'
            );
        }

        Config::$serverKey = $serverKey;

        Config::$clientKey = $clientKey;

        Config::$isProduction = config(
            'services.midtrans.is_production',
            false
        );

        Config::$isSanitized = true;

        Config::$is3ds = true;
    }

    /**
     * Get available gateways.
     */
    public function getAvailableGateways(): array
    {
        return $this->gateways;
    }

    /**
     * Get available methods.
     */
    public function getAvailableMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get methods by gateway.
     */
    public function getMethodsByGateway(
        string $gateway
    ): array {

        return $this->methods[
            $this->normalizeGateway(
                $gateway
            )
        ] ?? [];
    }

    /**
     * Check gateway validity.
     */
    public function isValidGateway(
        string $gateway
    ): bool {

        return in_array(

            $this->normalizeGateway(
                $gateway
            ),

            $this->gateways,

            true
        );
    }

    /**
     * Check payment method validity.
     */
    public function isValidMethod(
        string $gateway,
        string $method
    ): bool {

        return in_array(

            $this->normalizeMethod(
                $method
            ),

            $this->getMethodsByGateway(
                $gateway
            ),

            true
        );
    }

    /**
     * Validate gateway and method.
     */
    protected function validateGatewayAndMethod(
        string $gateway,
        string $method
    ): void {

        if (
            ! $this->isValidGateway(
                $gateway
            )
        ) {

            throw ValidationException::withMessages([

                'gateway' => [

                    'Gateway pembayaran tidak valid.',
                ],
            ]);
        }

        if (
            ! $this->isValidMethod(
                $gateway,
                $method
            )
        ) {

            throw ValidationException::withMessages([

                'method' => [

                    'Metode pembayaran tidak tersedia.',
                ],
            ]);
        }
    }
    /**
     * Build Midtrans Snap payload.
     */
    public function buildSnapPayload(
        Order $order,
        Payment $payment
    ): array {

        $this->validateGatewayAndMethod(
            $payment->gateway,
            $payment->method
        );

        if (
            (float) $payment->amount <= 0
        ) {

            throw ValidationException::withMessages([

                'amount' => [

                    'Nominal pembayaran tidak valid.',
                ],
            ]);
        }

        return [

            'transaction_details' =>

                $this->buildTransactionDetails(
                    $payment
                ),

            'customer_details' =>

                $this->buildCustomerDetails(
                    $order
                ),

            'item_details' =>

                $this->buildItemDetails(
                    $order
                ),

            'enabled_payments' => [

                $this->normalizeMethod(
                    $payment->method
                ),
            ],

            'callbacks' => [

                'finish'
                    => config(
                        'app.frontend_url'
                    ) . '/payment/success',

                'error'
                    => config(
                        'app.frontend_url'
                    ) . '/payment/failed',

                'pending'
                    => config(
                        'app.frontend_url'
                    ) . '/payment/pending',
            ],

            'expiry'
                => $this->generateExpiry(),
        ];
    }

    /**
     * Create Midtrans Snap token.
     */
    public function createSnapToken(
        Order $order,
        Payment $payment
    ): string {

        $payload = $this->buildSnapPayload(
            $order,
            $payment
        );

        return Snap::getSnapToken(
            $payload
        );
    }

    /**
     * Create Midtrans Snap redirect URL.
     */
    public function createSnapRedirectUrl(
        Order $order,
        Payment $payment
    ): string {

        $payload = $this->buildSnapPayload(
            $order,
            $payment
        );

        $response = Snap::createTransaction(
            $payload
        );

        return $response[
            'redirect_url'
        ] ?? '';
    }
    /**
     * Get Midtrans notification instance.
     */
    public function getNotification(): Notification
    {
        return new Notification();
    }

    /**
     * Translate Midtrans transaction status.
     */
    public function translateMidtransStatus(
        string $transactionStatus,
        ?string $fraudStatus = null
    ): string {

        return match ($transactionStatus) {

            'capture' =>

                $fraudStatus === 'challenge'
                    ? Payment::STATUS_PENDING
                    : Payment::STATUS_PAID,

            'settlement' =>
                Payment::STATUS_PAID,

            'pending' =>
                Payment::STATUS_PENDING,

            'deny' =>
                Payment::STATUS_FAILED,

            'cancel' =>
                Payment::STATUS_CANCELLED,

            'expire' =>
                Payment::STATUS_EXPIRED,

            'refund' =>
                Payment::STATUS_REFUNDED,

            'partial_refund' =>
                Payment::STATUS_PARTIAL_REFUND,

            'failure' =>
                Payment::STATUS_FAILED,

            default =>
                Payment::STATUS_PENDING,
        };
    }

    /**
     * Verify Midtrans notification signature.
     */
    public function verifyNotification(
        Notification $notification
    ): bool {

        $signatureKey =
            $notification->signature_key
            ?? null;

        if (blank($signatureKey)) {
            return false;
        }

        $expectedSignature = hash(
            'sha512',

            $notification->order_id
            . $notification->status_code
            . $notification->gross_amount
            . config(
                'services.midtrans.server_key'
            )
        );

        return hash_equals(
            $expectedSignature,
            $signatureKey
        );
    }

    /**
     * Handle Midtrans notification.
     *
     * This method only normalizes
     * Midtrans payload.
     *
     * Database updates must be handled
     * by PaymentService.
     */
    public function handleNotification(): array
    {
        $notification =
            $this->getNotification();

        if (
            ! $this->verifyNotification(
                $notification
            )
        ) {

            throw ValidationException::withMessages([

                'signature' => [

                    'Signature Midtrans tidak valid.',
                ],
            ]);
        }

        return [

            'payment_number'
                => $notification->order_id,

            'gateway_transaction_id'
                => $notification->transaction_id
                ?? null,

            'gateway_order_id'
                => $notification->order_id
                ?? null,

            'transaction_status'
                => $notification->transaction_status
                ?? null,

            'fraud_status'
                => $notification->fraud_status
                ?? null,

            'payment_type'
                => $notification->payment_type
                ?? null,

            'status'
                => $this->translateMidtransStatus(

                    $notification->transaction_status,

                    $notification->fraud_status
                        ?? null
                ),

            'gross_amount'
                => isset(
                    $notification->gross_amount
                )
                    ? (float)
                        $notification->gross_amount
                    : null,

            'transaction_time'
                => $notification->transaction_time
                ?? null,

            'settlement_time'
                => $notification->settlement_time
                ?? null,

            'currency'
                => $notification->currency
                ?? null,

            'payload'
                => json_decode(

                    json_encode(
                        $notification
                    ),

                    true
                ),
        ];
    }
    /**
     * Generate Midtrans expiry configuration.
     */
    public function generateExpiry(
        int $duration = 24,
        string $unit = 'hour'
    ): array {

        return [

            'start_time'
                => now()->format(
                    'Y-m-d H:i:s O'
                ),

            'unit'
                => strtolower(
                    trim($unit)
                ),

            'duration'
                => $duration,
        ];
    }

    /**
     * Normalize gateway name.
     */
    public function normalizeGateway(
        string $gateway
    ): string {

        return strtolower(
            trim($gateway)
        );
    }

    /**
     * Normalize payment method.
     */
    public function normalizeMethod(
        string $method
    ): string {

        return strtolower(
            trim($method)
        );
    }

    /**
     * Build customer details payload.
     */
    public function buildCustomerDetails(
        Order $order
    ): array {

        return [

            'first_name'
                => $order->customer_name,

            'email'
                => $order->customer_email,

            'phone'
                => $order->customer_phone,

            'billing_address'
                => $this->buildShippingAddress(
                    $order
                ),

            'shipping_address'
                => $this->buildShippingAddress(
                    $order
                ),
        ];
    }

    /**
     * Build shipping address payload.
     */
    public function buildShippingAddress(
        Order $order
    ): array {

        return [

            'first_name'
                => $order->recipient_name,

            'phone'
                => $order->recipient_phone,

            'address'
                => $order->shipping_address,

            'city'
                => $order->city,

            'postal_code'
                => $order->postal_code,

            'country_code'
                => 'IDN',
        ];
    }
    /**
     * Build Midtrans item details.
     */
    protected function buildItemDetails(
        Order $order
    ): array {

        $items = [];

        foreach ($order->items as $item) {

            $items[] = [

                'id'
                    => (string) (
                        $item->product_sku_id
                        ?? $item->id
                    ),

                'price'
                    => (int) round(
                        (float) $item->final_price
                    ),

                'quantity'
                    => (int) $item->quantity,

                'name'
                    => substr(
                        $item->productDisplayName(),
                        0,
                        50
                    ),
            ];
        }

        if (
            (float) $order->shipping_cost > 0
        ) {

            $items[] = [

                'id'
                    => 'SHIPPING',

                'price'
                    => (int) round(
                        (float) $order->shipping_cost
                    ),

                'quantity'
                    => 1,

                'name'
                    => 'Biaya Pengiriman',
            ];
        }

        if (
            (float) $order->tax_amount > 0
        ) {

            $items[] = [

                'id'
                    => 'TAX',

                'price'
                    => (int) round(
                        (float) $order->tax_amount
                    ),

                'quantity'
                    => 1,

                'name'
                    => 'Pajak',
            ];
        }

        if (
            (float) $order->discount_amount > 0
        ) {

            $items[] = [

                'id'
                    => 'DISCOUNT',

                'price'
                    => -1 * (int) round(
                        (float) $order->discount_amount
                    ),

                'quantity'
                    => 1,

                'name'
                    => 'Diskon',
            ];
        }

        return $items;
    }

    /**
     * Build Midtrans transaction details.
     */
    protected function buildTransactionDetails(
        Payment $payment
    ): array {

        return [

            'order_id'
                => $payment->payment_number,

            'gross_amount'
                => (int) round(
                    (float) $payment->amount
                ),
        ];
    }

    /**
     * Validate item total consistency.
     *
     * Midtrans requires:
     * gross_amount = total item_details.
     */
    protected function validateGrossAmount(
        Payment $payment,
        array $items
    ): void {

        $total = 0;

        foreach ($items as $item) {

            $total +=

                (
                    (int) $item['price']
                    *
                    (int) $item['quantity']
                );
        }

        $grossAmount = (int) round(
            (float) $payment->amount
        );

        if ($total !== $grossAmount) {

            throw ValidationException::withMessages([

                'gross_amount' => [

                    "Total item_details ({$total}) tidak sama dengan gross_amount ({$grossAmount}).",
                ],
            ]);
        }
    }

    /**
     * Get Midtrans configuration.
     */
    public function getMidtransConfig(): array
    {
        return [

            'server_key'
                => config(
                    'services.midtrans.server_key'
                ),

            'client_key'
                => config(
                    'services.midtrans.client_key'
                ),

            'is_production'
                => config(
                    'services.midtrans.is_production',
                    false
                ),

            'is_sanitized'
                => Config::$isSanitized,

            'is_3ds'
                => Config::$is3ds,
        ];
    }
}