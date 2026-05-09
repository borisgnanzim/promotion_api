<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    /**
     * Success response method.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     */
    protected function successResponse($data, $message = '', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function successResponseWithPaginate($ressourceCollectionClass, $paginatedCollection, $collectionName = 'data')
    {
        return $this->successResponse([
            "$collectionName" => $ressourceCollectionClass::collection($paginatedCollection),
            'links' => [
                'first' => $paginatedCollection->url(1),
                'last' => $paginatedCollection->url($paginatedCollection->lastPage()),
                'prev' => $paginatedCollection->previousPageUrl(),
                'next' => $paginatedCollection->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginatedCollection->currentPage(),
                'from' => $paginatedCollection->firstItem(),
                'last_page' => $paginatedCollection->lastPage(),
                'path' => $paginatedCollection->path(),
                'per_page' => $paginatedCollection->perPage(),
                'to' => $paginatedCollection->lastItem(),
                'total' => $paginatedCollection->total(),
            ],
        ]);
    }

    /**
     * Error response method.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed|null  $data
     */
    protected function errorResponse($message, $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
