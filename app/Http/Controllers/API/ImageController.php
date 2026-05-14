<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    use JsonResponseTrait;

    public function index()
    {
        return $this->successResponse(ImageResource::collection(Image::all()));
    }

    public function store(StoreImageRequest $request)
    {
        $image = Image::create($request->validated());

        return $this->successResponse(new ImageResource($image), 'Image created.', 201);
    }

    public function show($ref)
    {
        $image= Image::where('ref', $ref)->first();
        if($image == null)
        {
            return $this->errorResponse('Image not found', 404);
        }

        if (Storage::disk('public')->exists( $image->path)) {
            return response()->file(Storage::disk('public')->path( $image->path));
        }
        $filePath = storage_path('/images/placeholders/default.png');
        //$filePath = "https://picsum.photos/680/480";
        return response()->file($filePath);
    }

    public function update(UpdateImageRequest $request, Image $image)
    {
        $image->update($request->validated());

        return $this->successResponse(new ImageResource($image), 'Image updated.');
    }

    public function destroy(Image $image)
    {
        $image->delete();

        return $this->successResponse(null, 'Image deleted.');
    }
}
