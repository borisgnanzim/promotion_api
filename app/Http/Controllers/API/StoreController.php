<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Traits\JsonResponseTrait;

class StoreController extends Controller
{
    use JsonResponseTrait;

    /**
     * List all stores
     *
     * @group Stores
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "store_123", "name": "Main Store", "city": "Casablanca"}], "message": null}
     */
    public function index()
    {
        $stores = Store::with('sellers')->get();
        return $this->successResponse(StoreResource::collection($stores));
    }

    /**
     * Create a new store
     *
     * @group Stores
     * @authenticated
     * @bodyParam name string required The store name. Example: Main Store
     * @bodyParam address string required Store address. Example: 123 Main Street
     * @bodyParam city string required Store city. Example: Casablanca
     * @bodyParam country string required Store country. Example: Morocco
     * @bodyParam phone string Store phone number. Example: +212612345678
     * @bodyParam email string Store email. Example: store@example.com
     * @response 201 {"success": true, "data": {"id": 1, "ref": "store_123", "name": "Main Store"}, "message": "Store created."}
     */
    public function store(StoreStoreRequest $request)
    {
        $store = Store::create($request->validated());

        return $this->successResponse(new StoreResource($store), 'Store created.', 201);
    }

    /**
     * Get a specific store
     *
     * @group Stores
     * @authenticated
     * @urlParam ref string required The store reference. Example: store_123
     * @response 200 {"success": true, "data": {"id": 1, "ref": "store_123", "name": "Main Store"}, "message": null}
     */
    public function show(Store $store)
    {
        return $this->successResponse(new StoreResource($store));
    }

    /**
     * Update a store
     *
     * @group Stores
     * @authenticated
     * @urlParam ref string required The store reference. Example: store_123
     * @bodyParam name string Store name. Example: Main Store Updated
     * @bodyParam city string Store city. Example: Fez
     * @response 200 {"success": true, "data": {"id": 1, "ref": "store_123", "name": "Main Store Updated"}, "message": "Store updated."}
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $store->update($request->validated());

        return $this->successResponse(new StoreResource($store), 'Store updated.');
    }

    /**
     * Delete a store
     *
     * @group Stores
     * @authenticated
     * @urlParam ref string required The store reference. Example: store_123
     * @response 200 {"success": true, "data": null, "message": "Store deleted."}
     */
    public function destroy(Store $store)
    {
        $store->delete();

        return $this->successResponse(null, 'Store deleted.');
    }
}
