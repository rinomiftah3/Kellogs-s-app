<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreCustomerProfileRequest;
use App\Http\Requests\UpdateCustomerProfileRequest;

use App\Http\Resources\V1\CustomerProfileResource;

use App\Models\CustomerProfile;

use App\Services\CustomerService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use RuntimeException;
use Throwable;

/**
 * CustomerProfileController
 *
 * Enterprise Customer Management
 */
class CustomerProfileController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | List Customers
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {

        $customers = $this->customerService
            ->paginate(
                filters: [

                    'search'
                        => $request->input('search'),

                    'membership_level'
                        => $request->input(
                            'membership_level'
                        ),

                    'is_active'
                        => $request->has('is_active')
                            ? filter_var(
                                $request->input(
                                    'is_active'
                                ),
                                FILTER_VALIDATE_BOOLEAN
                            )
                            : null,

                    'has_orders'
                        => $request->has('has_orders')
                            ? filter_var(
                                $request->input(
                                    'has_orders'
                                ),
                                FILTER_VALIDATE_BOOLEAN
                            )
                            : null,

                    'email_subscribed'
                        => $request->has(
                            'email_subscribed'
                        )
                            ? filter_var(
                                $request->input(
                                    'email_subscribed'
                                ),
                                FILTER_VALIDATE_BOOLEAN
                            )
                            : null,

                    'sms_subscribed'
                        => $request->has(
                            'sms_subscribed'
                        )
                            ? filter_var(
                                $request->input(
                                    'sms_subscribed'
                                ),
                                FILTER_VALIDATE_BOOLEAN
                            )
                            : null,

                    'push_subscribed'
                        => $request->has(
                            'push_subscribed'
                        )
                            ? filter_var(
                                $request->input(
                                    'push_subscribed'
                                ),
                                FILTER_VALIDATE_BOOLEAN
                            )
                            : null,
                ],

                perPage: (int) $request->input(
                    'per_page',
                    15
                )
            );

        return response()->json([

            'message'
                => 'Daftar customer profile berhasil diambil.',

            'data'
                => CustomerProfileResource::collection(
                    $customers
                ),

            'meta' => [

                'current_page'
                    => $customers->currentPage(),

                'last_page'
                    => $customers->lastPage(),

                'per_page'
                    => $customers->perPage(),

                'total'
                    => $customers->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Customer
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreCustomerProfileRequest $request
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->create(
                    $request->validated()
                );

            return response()->json([

                'message'
                    => 'Customer profile berhasil dibuat.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ], 201);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal membuat customer profile.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Customer
    |--------------------------------------------------------------------------
    */

    public function show(
        CustomerProfile $customerProfile
    ): JsonResponse {

        $customer = $this->customerService
            ->findOrFail(
                $customerProfile
            );

        return response()->json([

            'message'
                => 'Detail customer profile berhasil diambil.',

            'data'
                => new CustomerProfileResource(
                    $customer
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Customer
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateCustomerProfileRequest $request,
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->update(
                    $customerProfile,
                    $request->validated()
                );

            return response()->json([

                'message'
                    => 'Customer profile berhasil diperbarui.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal memperbarui customer profile.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Customer
    |--------------------------------------------------------------------------
    */

    public function destroy(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $this->customerService
                ->delete(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Customer profile berhasil dihapus.',
            ]);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menghapus customer profile.',
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Activate Customer
    |--------------------------------------------------------------------------
    */

    public function activate(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->activate(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Customer profile berhasil diaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal mengaktifkan customer profile.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Deactivate Customer
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->deactivate(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Customer profile berhasil dinonaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menonaktifkan customer profile.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Change Membership
    |--------------------------------------------------------------------------
    */

    public function changeMembership(
        Request $request,
        CustomerProfile $customerProfile
    ): JsonResponse {

        $validated = $request->validate([

            'membership_level' => [

                'required',

                'string',

                'in:' . implode(',', [

                    CustomerProfile::LEVEL_REGULAR,

                    CustomerProfile::LEVEL_SILVER,

                    CustomerProfile::LEVEL_GOLD,

                    CustomerProfile::LEVEL_PLATINUM,
                ]),
            ],
        ]);

        try {

            $customer = $this->customerService
                ->changeMembership(
                    $customerProfile,
                    $validated[
                        'membership_level'
                    ]
                );

            return response()->json([

                'message'
                    => 'Membership customer berhasil diperbarui.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal mengubah membership customer.',
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Increase Customer Points
    |--------------------------------------------------------------------------
    */

    public function increasePoints(
        Request $request,
        CustomerProfile $customerProfile
    ): JsonResponse {

        $validated = $request->validate([

            'points' => [

                'required',

                'integer',

                'min:1',
            ],
        ]);

        try {

            $customer = $this->customerService
                ->increasePoints(
                    $customerProfile,
                    $validated['points']
                );

            return response()->json([

                'message'
                    => 'Poin customer berhasil ditambahkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menambahkan poin customer.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Increase Order Statistics
    |--------------------------------------------------------------------------
    */

    public function increaseOrderCount(
        Request $request,
        CustomerProfile $customerProfile
    ): JsonResponse {

        $validated = $request->validate([

            'total_spent' => [

                'nullable',

                'numeric',

                'min:0',
            ],
        ]);

        try {

            $customer = $this->customerService
                ->increaseOrderCount(
                    $customerProfile,
                    (float) (
                        $validated['total_spent']
                        ?? 0
                    )
                );

            return response()->json([

                'message'
                    => 'Statistik pesanan customer berhasil diperbarui.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal memperbarui statistik pesanan customer.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Last Order Timestamp
    |--------------------------------------------------------------------------
    */

    public function updateLastOrder(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->updateLastOrder(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Waktu pesanan terakhir customer berhasil diperbarui.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal memperbarui waktu pesanan terakhir customer.',
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Subscribe Email Notification
    |--------------------------------------------------------------------------
    */

    public function subscribeEmail(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->subscribeEmail(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan email berhasil diaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal mengaktifkan langganan email.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Unsubscribe Email Notification
    |--------------------------------------------------------------------------
    */

    public function unsubscribeEmail(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->unsubscribeEmail(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan email berhasil dinonaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menonaktifkan langganan email.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Subscribe SMS Notification
    |--------------------------------------------------------------------------
    */

    public function subscribeSms(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->subscribeSms(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan SMS berhasil diaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal mengaktifkan langganan SMS.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Unsubscribe SMS Notification
    |--------------------------------------------------------------------------
    */

    public function unsubscribeSms(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->unsubscribeSms(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan SMS berhasil dinonaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menonaktifkan langganan SMS.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Subscribe Push Notification
    |--------------------------------------------------------------------------
    */

    public function subscribePush(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->subscribePush(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan push notification berhasil diaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal mengaktifkan push notification.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Unsubscribe Push Notification
    |--------------------------------------------------------------------------
    */

    public function unsubscribePush(
        CustomerProfile $customerProfile
    ): JsonResponse {

        try {

            $customer = $this->customerService
                ->unsubscribePush(
                    $customerProfile
                );

            return response()->json([

                'message'
                    => 'Langganan push notification berhasil dinonaktifkan.',

                'data'
                    => new CustomerProfileResource(
                        $customer
                    ),
            ]);

        } catch (RuntimeException $exception) {

            return response()->json([

                'message'
                    => $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'message'
                    => 'Gagal menonaktifkan push notification.',
            ], 500);
        }
    }
}