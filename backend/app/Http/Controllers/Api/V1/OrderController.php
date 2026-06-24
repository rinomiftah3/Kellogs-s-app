<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;

use App\Http\Resources\V1\OrderResource;

use App\Models\Order;

use App\Services\OrderService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Order service instance.
     */
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | List Orders
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {

        $filters = [

            'search' =>
                $request->input('search'),

            'status' =>
                $request->input('status'),

            'payment_status' =>
                $request->input('payment_status'),

            'fulfillment_status' =>
                $request->input('fulfillment_status'),

            'customer_profile_id' =>
                $request->input('customer_profile_id'),
        ];

        $perPage = (int) $request->input(
            'per_page',
            15
        );

        $orders = $this->orderService
            ->paginate(
                filters: $filters,
                perPage: $perPage
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Daftar pesanan berhasil diambil.',

            'data' =>
                OrderResource::collection(
                    $orders
                ),

            'meta' => [

                'current_page' =>
                    $orders->currentPage(),

                'last_page' =>
                    $orders->lastPage(),

                'per_page' =>
                    $orders->perPage(),

                'total' =>
                    $orders->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Order Detail
    |--------------------------------------------------------------------------
    */

    public function show(
        Order $order
    ): JsonResponse {

        $order = $this->orderService
            ->findOrFail(
                $order->id
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Detail pesanan berhasil diambil.',

            'data' =>
                new OrderResource(
                    $order
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Order Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ): JsonResponse {

        $validated = $request->validated();

        $order = $this->orderService
            ->updateStatus(
                $order,
                $validated['status']
            );

        if (
            array_key_exists(
                'admin_notes',
                $validated
            )
        ) {

            $order = $this->orderService
                ->update(
                    $order,
                    [
                        'admin_notes' =>
                            $validated['admin_notes'],
                    ]
                );
        }

        return response()->json([

            'success' => true,

            'message' =>
                'Status pesanan berhasil diperbarui.',

            'data' =>
                new OrderResource(
                    $order
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Order
    |--------------------------------------------------------------------------
    */

    public function cancel(
        CancelOrderRequest $request,
        Order $order
    ): JsonResponse {

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Future Ready
        |--------------------------------------------------------------------------
        |
        | Reason sudah divalidasi oleh request.
        | Nanti akan digunakan oleh OrderService
        | untuk membuat OrderHistory / StatusLog.
        |
        */

        $reason =
            $validated['reason']
            ?? null;

        $order = $this->orderService
            ->cancel(
                $order
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Pesanan berhasil dibatalkan.',

            'data' =>
                new OrderResource(
                    $order
                ),
        ]);
    }
}