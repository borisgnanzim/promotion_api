<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;
use App\Traits\JsonResponseTrait;

class StoreController extends Controller
{
    use JsonResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse(Store::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        $store = Store::create($request->validated());

        return $this->successResponse($store, 'Store created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return $this->successResponse($store);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $store->update($request->validated());

        return $this->successResponse($store, 'Store updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();

        return $this->successResponse(null, 'Store deleted.');
    }
}
