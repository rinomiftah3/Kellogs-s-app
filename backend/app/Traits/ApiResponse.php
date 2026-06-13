<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /*
    |--------------------------------------------------------------------------
    | Success Response
    |--------------------------------------------------------------------------
    */

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {

        return response()->json([
            'success' => true,

            'message' => $message,

            'data' => $data,

            'meta' => $meta,

            'timestamp' => now()->toISOString(),

            'request_id' => $this->requestId(),
        ], $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Created Response
    |--------------------------------------------------------------------------
    */

    protected function createdResponse(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {

        return $this->successResponse(
            $data,
            $message,
            201
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Deleted Response
    |--------------------------------------------------------------------------
    */

    protected function deletedResponse(
        string $message = 'Deleted successfully'
    ): JsonResponse {

        return $this->successResponse(
            null,
            $message
        );
    }

    /*
    |--------------------------------------------------------------------------
    | No Content Response
    |--------------------------------------------------------------------------
    */

    protected function noContentResponse(): JsonResponse
    {
        return response()->json(
            null,
            204
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Error Response
    |--------------------------------------------------------------------------
    */

    protected function errorResponse(
        string $message,
        int $status = 400,
        array $errors = [],
        ?string $code = null
    ): JsonResponse {

        return response()->json([
            'success' => false,

            'message' => $message,

            'code' => $code,

            'errors' => $errors,

            'timestamp' => now()->toISOString(),

            'request_id' => $this->requestId(),
        ], $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Paginated Response
    |--------------------------------------------------------------------------
    */

    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = 'Data berhasil diambil'
    ): JsonResponse {

        return response()->json([
            'success' => true,

            'message' => $message,

            'data' => $paginator->items(),

            'meta' => [

                'current_page' =>
                    $paginator->currentPage(),

                'last_page' =>
                    $paginator->lastPage(),

                'per_page' =>
                    $paginator->perPage(),

                'total' =>
                    $paginator->total(),

                'from' =>
                    $paginator->firstItem(),

                'to' =>
                    $paginator->lastItem(),
            ],

            'timestamp' =>
                now()->toISOString(),

            'request_id' =>
                $this->requestId(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Request ID Helper
    |--------------------------------------------------------------------------
    */

    protected function requestId(): string
    {
        return request()->header(
            'X-Request-Id'
        ) ?? Str::uuid()->toString();
    }
}