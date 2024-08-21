<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Models\ImageFiles;
use App\Services\ImageFileService;

class ImageFileController extends BaseController
{
    protected $imageFileService;

    // in this controller we have call the ImageFileService where all the model working will be perfrom on this service
    public function __construct(ImageFileService $imageFileService)
    {
        $this->imageFileService = $imageFileService;
    }

    // it is used to fetch  details imaged
    public function show(ImageFiles $imagefiles)
    {
        if ($imagefiles) {
            return $this->success(
                new ImageFileResource($imagefiles),
                'Image files details'
            );
        }

        return $this->errors(
            ['message' => 'Image file details not found'],
            400
        );
    }

    // it is used to delete the image and files
    public function destroy(ImageFiles $imagefiles)
    {
        if ($imagefiles) {
            $this->imageFileService->deleteFile($imagefiles);
            $imagefiles->delete();

            return $this->success(
                new ImageFileResource($imagefiles),
                'Image files deleted successfully'
            );
        }

        return $this->errors(
            ['message' => 'Image file details not found'],
            400
        );
    }
}
