<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Traits\JsonResponseTrait;

class PromotionController extends Controller
{
    use JsonResponseTrait;

    /**
     * List all promotions
     *
     * @group Promotions
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "promo_123", "name": "Summer Sale", "description": "50% off"}], "message": null}
     */
    public function index()
    {
        return $this->successResponse(PromotionResource::collection(Promotion::all()));
    }

    /**
     * Create a new promotion
     *
     * @group Promotions
     * @authenticated
     * @bodyParam name string required The promotion name. Example: Summer Sale
     * @bodyParam description string The promotion description. Example: 50% off all items
     * @bodyParam start_date date required Promotion start date. Example: 2026-06-01
     * @bodyParam end_date date required Promotion end date. Example: 2026-08-31
     * @bodyParam discount_type string required Type of discount (percentage or fixed). Example: percentage
     * @bodyParam discount_value number required Discount value. Example: 50
     * @response 201 {"success": true, "data": {"id": 1, "ref": "promo_123", "name": "Summer Sale"}, "message": "Promotion created."}
     */
    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        return $this->successResponse(new PromotionResource($promotion), 'Promotion created.', 201);
    }

    /**
     * Get a specific promotion
     *
     * @group Promotions
     * @authenticated
     * @urlParam ref string required The promotion reference. Example: promo_123
     * @response 200 {"success": true, "data": {"id": 1, "ref": "promo_123", "name": "Summer Sale"}, "message": null}
     */
    public function show(Promotion $promotion)
    {
        return $this->successResponse(new PromotionResource($promotion));
    }

    /**
     * Update a promotion
     *
     * @group Promotions
     * @authenticated
     * @urlParam ref string required The promotion reference. Example: promo_123
     * @bodyParam name string The promotion name. Example: Summer Sale Updated
     * @bodyParam discount_value number Discount value. Example: 60
     * @response 200 {"success": true, "data": {"id": 1, "ref": "promo_123", "name": "Summer Sale Updated"}, "message": "Promotion updated."}
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $promotion->update($request->validated());

        return $this->successResponse(new PromotionResource($promotion), 'Promotion updated.');
    }

    /**
     * Delete a promotion
     *
     * @group Promotions
     * @authenticated
     * @urlParam ref string required The promotion reference. Example: promo_123
     * @response 200 {"success": true, "data": null, "message": "Promotion deleted."}
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return $this->successResponse(null, 'Promotion deleted.');
    }
}
