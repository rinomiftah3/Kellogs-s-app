<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\PaymentCallbackRequest;

use App\Http\Resources\V1\PaymentResource;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\PaymentMethodService;
use App\Services\PaymentService;

use Illuminate\Http\JsonResponse;

class PaymentMethodController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        protected PaymentMethodService $paymentMethodService,
        protected PaymentService $paymentService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Get Available Gateways
    |--------------------------------------------------------------------------
    */

    public function gateways(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' => 'Daftar gateway pembayaran berhasil diambil.',

            'data' => $this->paymentMethodService
                ->getAvailableGateways(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Available Methods
    |--------------------------------------------------------------------------
    */

    public function methods(
        string $gateway
    ): JsonResponse {

        return response()->json([

            'success' => true,

            'message' => 'Daftar metode pembayaran berhasil diambil.',

            'data' => [

                'gateway' => $gateway,

                'methods' => $this->paymentMethodService
                    ->getMethodsByGateway(
                        $gateway
                    ),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Snap Token
    |--------------------------------------------------------------------------
    */

    public function snapToken(
        Payment $payment
    ): JsonResponse {

        $payment = $this->paymentService
            ->findOrFail(
                $payment->id
            );

        $order = $payment->order;

        $token = $this->paymentMethodService
            ->createSnapToken(
                $order,
                $payment
            );

        return response()->json([

            'success' => true,

            'message' => 'Snap token berhasil dibuat.',

            'data' => [

                'payment' => new PaymentResource(
                    $payment
                ),

                'snap_token' => $token,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Redirect URL
    |--------------------------------------------------------------------------
    */

    public function redirectUrl(
        Payment $payment
    ): JsonResponse {

        $payment = $this->paymentService
            ->findOrFail(
                $payment->id
            );

        $order = $payment->order;

        $redirectUrl = $this->paymentMethodService
            ->createSnapRedirectUrl(
                $order,
                $payment
            );

        $payment = $this->paymentService
            ->update(
                $payment,
                [
                    'payment_url' => $redirectUrl,
                ]
            );

        return response()->json([

            'success' => true,

            'message' => 'Redirect URL berhasil dibuat.',

            'data' => [

                'payment' => new PaymentResource(
                    $payment
                ),

                'redirect_url' => $redirectUrl,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Midtrans Callback
    |--------------------------------------------------------------------------
    */

    public function callback(
        PaymentCallbackRequest $request
    ): JsonResponse {

        try {

            $notification = $this->paymentMethodService
                ->handleNotification();

            $payment = $this->paymentService
                ->findByPaymentNumber(
                    $notification['payment_number']
                );

            if (! $payment) {

                return response()->json([

                    'success' => false,

                    'message' => 'Pembayaran tidak ditemukan.',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Save Callback Log
            |--------------------------------------------------------------------------
            */

            $callback = $this->paymentService
                ->createCallback(
                    $payment,
                    [

                        'gateway'
                            => $payment->gateway,

                        'event_type'
                            => $notification['transaction_status'],

                        'gateway_transaction_id'
                            => $notification['gateway_transaction_id'],

                        'gateway_order_id'
                            => $notification['gateway_order_id'],

                        'signature_valid'
                            => true,

                        'payload'
                            => $notification['payload'],

                        'received_at'
                            => now(),
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | Update Payment Status
            |--------------------------------------------------------------------------
            */
$this->paymentService
    ->ensureTransactionNotProcessed(
        $notification['gateway_transaction_id']
    );
            switch (
                $notification['status']
            ) {

                case Payment::STATUS_PAID:

    $this->paymentService
        ->createTransaction(
            $payment,
            [

                'gateway_transaction_id'
                    => $notification['gateway_transaction_id'],

                'gateway_order_id'
                    => $notification['gateway_order_id'],

                'gateway'
                    => $payment->gateway,

                'method'
                    => $notification['payment_type']
                        ?? $payment->method,

                'type'
                    => PaymentTransaction::TYPE_SETTLEMENT,

                'amount'
                    => $notification['gross_amount'],

                'status'
                    => PaymentTransaction::STATUS_SUCCESS,

                'processed_at'
                    => now(),

                'response_payload'
                    => $notification['payload'],
            ]
        );

    $payment = $this->paymentService
        ->markAsPaid(
            $payment,
            $notification['gross_amount']
        );

    break;

                case Payment::STATUS_FAILED:

                    $payment = $this->paymentService
                        ->markAsFailed(
                            $payment
                        );

                    break;

                case Payment::STATUS_EXPIRED:

                    $payment = $this->paymentService
                        ->markAsExpired(
                            $payment
                        );

                    break;

                case Payment::STATUS_CANCELLED:

                    $payment = $this->paymentService
                        ->markAsCancelled(
                            $payment
                        );

                    break;

                case Payment::STATUS_REFUNDED:

                    $payment = $this->paymentService
                        ->refund(
                            $payment,
                            (float) (
                                $notification['gross_amount']
                                ?? 0
                            )
                        );

                    break;
            }

            $this->paymentService
                ->markCallbackProcessed(
                    $callback,
                    [
                        'status'
                            => 'success',
                    ]
                );

            return response()->json([

                'success' => true,

                'message' => 'Callback pembayaran berhasil diproses.',

                'data' => new PaymentResource(
                    $payment
                ),
            ]);
        } catch (\Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Midtrans Configuration
    |--------------------------------------------------------------------------
    */

    public function configuration(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' => 'Konfigurasi Midtrans berhasil diambil.',

            'data' => $this->paymentMethodService
                ->getMidtransConfig(),
        ]);
    }
}