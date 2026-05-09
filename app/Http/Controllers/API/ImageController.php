<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Image;
use App\Traits\JsonResponseTrait;

class ImageController extends Controller
{
    use JsonResponseTrait;

    public function index()
    {
        return $this->successResponse(Image::all());
    }

    public function store(StoreImageRequest $request)
    {
        $image = Image::create($request->validated());

        return $this->successResponse($image, 'Image created.', 201);
    }

    public function show(Image $image)
    {
        return $this->successResponse($image);
    }

    public function update(UpdateImageRequest $request, Image $image)
    {
        $image->update($request->validated());

        return $this->successResponse($image, 'Image updated.');
    }

    public function destroy(Image $image)
    {
        $image->delete();

        return $this->successResponse(null, 'Image deleted.');
    }
}
