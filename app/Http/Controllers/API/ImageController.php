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

    /**
     * List all images
     *
     * @group Images
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "img_123", "path": "/images/photo.jpg", "alt_text": "Sample image"}], "message": null}
     */
    public function index()
    {
        return $this->successResponse(ImageResource::collection(Image::all()));
    }

    /**
     * Upload a new image
     *
     * @group Images
     * @authenticated
     * @bodyParam path file required The image file to upload. Example: photo.jpg
     * @bodyParam alt_text string Alternative text for the image. Example: Sample image
     * @bodyParam description string Image description. Example: A beautiful landscape photo
     * @response 201 {"success": true, "data": {"id": 1, "ref": "img_123", "path": "/images/photo.jpg"}, "message": "Image created."}
     */
    public function store(StoreImageRequest $request)
    {
        $image = Image::create($request->validated());

        return $this->successResponse(new ImageResource($image), 'Image created.', 201);
    }

    /**
     * Get a specific image file
     *
     * @group Images
     * @authenticated
     * @urlParam ref string required The image reference. Example: img_123
     * @response 200 Returns the image file
     * @response 404 {"success": false, "message": "Image not found"}
     */
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

    /**
     * Update image metadata
     *
     * @group Images
     * @authenticated
     * @urlParam ref string required The image reference. Example: img_123
     * @bodyParam alt_text string Alternative text for the image. Example: Updated image text
     * @bodyParam description string Image description. Example: Updated description
     * @response 200 {"success": true, "data": {"id": 1, "ref": "img_123", "alt_text": "Updated image text"}, "message": "Image updated."}
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        $image->update($request->validated());

        return $this->successResponse(new ImageResource($image), 'Image updated.');
    }

    /**
     * Delete an image
     *
     * @group Images
     * @authenticated
     * @urlParam ref string required The image reference. Example: img_123
     * @response 200 {"success": true, "data": null, "message": "Image deleted."}
     */
    public function destroy(Image $image)
    {
        $image->delete();

        return $this->successResponse(null, 'Image deleted.');
    }
}
