<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;

use App\Http\Resources\V1\PaymentResource;

use App\Models\Payment;

use App\Services\PaymentService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Payment service instance.
     */
    public function __construct(
        protected PaymentService $paymentService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | List Payments
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {

        $filters = [

            'search' => $request->input('search'),

            'status' => $request->input('status'),

            'gateway' => $request->input('gateway'),
        ];

        $perPage = (int) $request->input(
            'per_page',
            15
        );

        $payments = $this->paymentService
            ->paginate(
                filters: $filters,
                perPage: $perPage
            );

        return response()->json([

            'success' => true,

            'message' => 'Daftar pembayaran berhasil diambil.',

            'data' => PaymentResource::collection(
                $payments
            ),

            'meta' => [

                'current_page'
                    => $payments->currentPage(),

                'last_page'
                    => $payments->lastPage(),

                'per_page'
                    => $payments->perPage(),

                'total'
                    => $payments->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Payment
    |--------------------------------------------------------------------------
    */

    public function store(
        StorePaymentRequest $request
    ): JsonResponse {

        $validated = $request->validated();

        $validated['payment_number'] =
            'PAY-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(
                substr(
                    uniqid(),
                    -6
                )
            );

        $payment = $this->paymentService
            ->create(
                $validated
            );

        return response()->json([

            'success' => true,

            'message' => 'Pembayaran berhasil dibuat.',

            'data' => new PaymentResource(
                $payment
            ),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Payment Detail
    |--------------------------------------------------------------------------
    */

    public function show(
        Payment $payment
    ): JsonResponse {

        $payment = $this->paymentService
            ->findOrFail(
                $payment->id
            );

        return response()->json([

            'success' => true,

            'message' => 'Detail pembayaran berhasil diambil.',

            'data' => new PaymentResource(
                $payment
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Payment Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        UpdatePaymentStatusRequest $request,
        Payment $payment
    ): JsonResponse {

        $validated = $request->validated();

        $status = $validated['status'];

        switch ($status) {

            case Payment::STATUS_PAID:

                $payment = $this->paymentService
                    ->markAsPaid(
                        $payment,
                        $validated['paid_amount']
                            ?? null
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

            case Payment::STATUS_PARTIAL_REFUND:

                $payment = $this->paymentService
                    ->refund(
                        $payment,
                        (float) $validated['refund_amount']
                    );

                break;

            default:

                throw ValidationException::withMessages([

                    'status' => [
                        'Status pembayaran tidak didukung.',
                    ],
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Additional Information
        |--------------------------------------------------------------------------
        */

        $updateData = [];

        if (
            array_key_exists(
                'gateway_transaction_id',
                $validated
            )
        ) {

            $updateData[
                'gateway_transaction_id'
            ] = $validated[
                'gateway_transaction_id'
            ];
        }

        if (
            array_key_exists(
                'notes',
                $validated
            )
        ) {

            $updateData[
                'notes'
            ] = $validated[
                'notes'
            ];
        }

        if (
            array_key_exists(
                'metadata',
                $validated
            )
        ) {

            $updateData[
                'metadata'
            ] = $validated[
                'metadata'
            ];
        }

        if (! empty($updateData)) {

            $payment = $this->paymentService
                ->update(
                    $payment,
                    $updateData
                );
        }

        return response()->json([

            'success' => true,

            'message'
                => 'Status pembayaran berhasil diperbarui.',

            'data'
                => new PaymentResource(
                    $payment
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Refund Payment
    |--------------------------------------------------------------------------
    */

    public function refund(
        Request $request,
        Payment $payment
    ): JsonResponse {

        $validated = $request->validate([

            'amount' => [

                'required',

                'numeric',

                'min:0.01',
            ],

            'notes' => [

                'nullable',

                'string',

                'max:1000',
            ],
        ]);

        $payment = $this->paymentService
            ->refund(

                payment: $payment,

                amount: (float) $validated['amount'],

                transactionData: [

                    'notes'
                        => $validated['notes']
                        ?? 'Refund payment.',
                ]
            );

        return response()->json([

            'success' => true,

            'message'
                => 'Refund pembayaran berhasil diproses.',

            'data'
                => new PaymentResource(
                    $payment
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Payment
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Payment $payment
    ): JsonResponse {

        $this->paymentService
            ->delete(
                $payment
            );

        return response()->json([

            'success' => true,

            'message'
                => 'Pembayaran berhasil dihapus.',
        ]);
    }
}