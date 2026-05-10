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

    public function index()
    {
        return $this->successResponse(PromotionResource::collection(Promotion::all()));
    }

    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        return $this->successResponse(new PromotionResource($promotion), 'Promotion created.', 201);
    }

    public function show(Promotion $promotion)
    {
        return $this->successResponse(new PromotionResource($promotion));
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $promotion->update($request->validated());

        return $this->successResponse(new PromotionResource($promotion), 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return $this->successResponse(null, 'Promotion deleted.');
    }
}
