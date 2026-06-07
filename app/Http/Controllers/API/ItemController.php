<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Traits\JsonResponseTrait;

class ItemController extends Controller
{
    use JsonResponseTrait;

    /**
     * List all items
     *
     * @group Items
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "item_123", "name": "Laptop", "description": "A powerful laptop", "price": 999.99, "category_ref": "cat_123"}], "message": null}
     */

    public function index()
    {
        return $this->successResponse(ItemResource::collection(Item::with('category', 'promotion', 'images')->get()));
    }

    /**
     * Create a new item
     *
     * @group Items
     * @authenticated
     * @bodyParam name string required The item name. Example: Laptop
     * @bodyParam description string The item description. Example: A powerful laptop
     * @bodyParam price number required The item price. Example: 999.99
     * @bodyParam category_ref string required The category reference. Example: cat_123
     * @response 201 {"success": true, "data": {"id": 1, "ref": "item_123", "name": "Laptop"}, "message": "Item created."}
     * @response 422 {"success": false, "message": "Validation failed"}
     */
    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());

        return $this->successResponse(new ItemResource($item), 'Item created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return $this->successResponse(new ItemResource($item));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @group Items
     * @authenticated
     * @bodyParam name string The item name. Example: Laptop
     * @bodyParam description string The item description. Example: A powerful laptop
     * @bodyParam price number The item price. Example: 999.99
     * @bodyParam category_ref string The category reference. Example: cat_123
     * @response 200 {"success": true, "data": {"id": 1, "ref": "item_123", "name": "Laptop"}, "message": "Item updated."}
     * @response 422 {"success": false, "message": "Validation failed"}
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        return $this->successResponse(new ItemResource($item), 'Item updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Items
     * @authenticated
     * @response 200 {"success": true, "data": null, "message": "Item deleted."}    
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return $this->successResponse(null, 'Item deleted.');
    }
}
