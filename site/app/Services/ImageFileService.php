<?php

namespace App\Services;

use App\Models\ImageFiles;
use Illuminate\Support\Facades\Storage;

class ImageFileService extends BaseService
{
    public function __construct(ImageFiles $imagefiles)
    {
        $this->model = $imagefiles;
    }

    // this function is used to create image files
    public function create($id, $files, $model, $folderName)
    {
        foreach ($files as $file) {
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $filename = time().'-'.$name;
            $filePath = $file->storeAs($folderName, $filename, 'public');
            $extension = $file->getClientOriginalExtension();
            $this->model->create([
                'image' => $filePath,
                'model_id' => $id,
                'model' => $model,
                'file_type' => $extension,
                'type' => $folderName,
            ]);
        }

        return true;
    }

    // this function is used to delete the images and files
    public function deleteFile($imagefiles)
    {
        return $this->deleteImage($imagefiles->image);
    }

    public function deleteBulkImage($ids)
    {
        $images = $this->model->whereIn('id', $ids)->get();
        Storage::disk('public')->delete(
            collect($images)->pluck('image')->toArray()
        );

        return $this->model->whereIn('id', $ids)->delete();
    }
}
