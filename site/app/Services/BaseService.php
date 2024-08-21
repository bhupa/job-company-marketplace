<?php

namespace App\Services;

use App\Models\Flip;
use App\Traits\JobTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BaseService
{
    use JobTraits;
    // return model
    protected $model;

    // fetch all the data of the model
    public function getAll()
    {
        return $this->model->get();
    }

    // this function will fetch the model by its id
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function latest()
    {
        return $this->model->latest()->first();
    }

    // this function will update the model
    public function updateModel($id, $inputs)
    {
        $update = $this->model->find($id);

        return $update->update($inputs);
    }

    // this function is used to get the paginate
    public function paginate($paginate)
    {
        return $this->model->paginate($paginate);
    }

    // this function will  upload the image
    public function uploadImg($file, $folder)
    {
        $name = str_replace(' ', '_', $file->getClientOriginalName());
        $filename = time().'-'.$name;
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->storeAs($folder, $filename, 'public');

        return $filePath;
    }

    // this function will delete the images
    public function deleteImage($path)
    {
        return Storage::delete('public/'.$path);
    }

    public function updateOrCreate($data)
    {
        return $this->model->updateOrCreate($data);
    }

    public function where_array($array)
    {
        return $this->model->where($array);
    }

    public function datetoTimeString($date)
    {
        $output = Carbon::parse($date);

        return $output->toDateTimeString();
    }

    public function countRequest()
    {
        $userRequest = Flip::where('user_id', auth()->id())
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->count();

        $countFavourite = Flip::where('user_id', auth()->id())
            ->where('type', 'favourite')
            ->count();

        return [
            'userRequest' => $userRequest,
            'countFavourite' => $countFavourite,
        ];
    }
}
