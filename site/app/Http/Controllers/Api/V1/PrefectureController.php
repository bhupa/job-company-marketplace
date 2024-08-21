<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\JobLocation\PrefectureDetailsResource;
use App\Models\V1\Prefecture;

class PrefectureController extends BaseController
{
    public function index()
    {
        $prefectures = Prefecture::orderBy('created_at', 'asc')->get();

        return $this->success(
            PrefectureDetailsResource::collection($prefectures),
            'Prefecture with district lists'
        );
    }
}
