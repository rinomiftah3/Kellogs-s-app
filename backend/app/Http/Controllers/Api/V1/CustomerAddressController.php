<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreCustomerAddressRequest;
use App\Http\Requests\UpdateCustomerAddressRequest;

use App\Http\Resources\V1\CustomerAddressResource;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;

use App\Services\CustomerAddressService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CustomerAddressController extends Controller
{
    public function __construct(
        protected CustomerAddressService $service
    ) {
    }

    /**
     * Display a listing of customer addresses.
     */
    public function index(
        Request $request
    ): JsonResponse {

        try {

            $filters = [

                'customer_profile_id'
                    => $request->input(
                        'customer_profile_id'
                    ),

                'province'
                    => $request->input(
                        'province'
                    ),

                'city'
                    => $request->input(
                        'city'
                    ),

                'postal_code'
                    => $request->input(
                        'postal_code'
                    ),

                'search'
                    => $request->input(
                        'search'
                    ),

                'is_default'
                    => $request->has(
                        'is_default'
                    )
                        ? filter_var(
                            $request->input(
                                'is_default'
                            ),
                            FILTER_VALIDATE_BOOLEAN
                        )
                        : null,

                'is_active'
                    => $request->has(
                        'is_active'
                    )
                        ? filter_var(
                            $request->input(
                                'is_active'
                            ),
                            FILTER_VALIDATE_BOOLEAN
                        )
                        : null,
            ];

            $perPage = (int)
                $request->input(
                    'per_page',
                    15
                );

            $addresses = $this->service
                ->paginate(
                    $filters,
                    $perPage
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Daftar alamat pelanggan berhasil diambil.',

                'data'
                    => CustomerAddressResource::collection(
                        $addresses
                    ),

                'meta' => [

                    'current_page'
                        => $addresses->currentPage(),

                    'last_page'
                        => $addresses->lastPage(),

                    'per_page'
                        => $addresses->perPage(),

                    'total'
                        => $addresses->total(),
                ],
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengambil daftar alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Store a newly created customer address.
     */
    public function store(
        StoreCustomerAddressRequest $request
    ): JsonResponse {

        try {

            $address = $this->service->create(
                $request->validated()
            );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat pelanggan berhasil dibuat.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ], 201);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal membuat alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified customer address.
     */
    public function show(
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $address = $this->service
                ->findOrFail(
                    $customerAddress
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Detail alamat pelanggan berhasil diambil.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengambil detail alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update the specified customer address.
     */
    public function update(
        UpdateCustomerAddressRequest $request,
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $address = $this->service->update(

                $customerAddress,

                $request->validated()
            );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat pelanggan berhasil diperbarui.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal memperbarui alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified customer address.
     */
    public function destroy(
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $this->service->delete(
                $customerAddress
            );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat pelanggan berhasil dihapus.',
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal menghapus alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Set address as default.
     */
    public function setDefault(
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $address = $this->service
                ->setDefault(
                    $customerAddress
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat utama berhasil diperbarui.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengubah alamat utama.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate address.
     */
    public function activate(
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $address = $this->service
                ->activate(
                    $customerAddress
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat berhasil diaktifkan.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengaktifkan alamat.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate address.
     */
    public function deactivate(
        CustomerAddress $customerAddress
    ): JsonResponse {

        try {

            $address = $this->service
                ->deactivate(
                    $customerAddress
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat berhasil dinonaktifkan.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal menonaktifkan alamat.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get addresses by customer.
     */
    public function byCustomer(
        CustomerProfile $customer
    ): JsonResponse {

        try {

            $addresses = $this->service
                ->getByCustomer(
                    $customer
                );

            return response()->json([

                'success' => true,

                'message'
                    => 'Daftar alamat pelanggan berhasil diambil.',

                'data'
                    => CustomerAddressResource::collection(
                        $addresses
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengambil alamat pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer's default address.
     */
    public function defaultAddress(
        CustomerProfile $customer
    ): JsonResponse {

        try {

            $address = $this->service
                ->getDefaultAddress(
                    $customer
                );

            if (! $address) {

                return response()->json([

                    'success' => false,

                    'message'
                        => 'Alamat utama pelanggan tidak ditemukan.',
                ], 404);
            }

            return response()->json([

                'success' => true,

                'message'
                    => 'Alamat utama pelanggan berhasil diambil.',

                'data'
                    => new CustomerAddressResource(
                        $address
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message'
                    => 'Gagal mengambil alamat utama pelanggan.',

                'error'
                    => $e->getMessage(),
            ], 500);
        }
    }
}