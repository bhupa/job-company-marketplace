<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\JobLocation\DistrictDetailsResource;
use App\Models\V1\District;

class JobLocationController extends BaseController
{
    public function index()
    {
        $districts = District::orderBy('created_at', 'asc')
            ->whereNull('parent_id')
            ->get();

        return $this->success(
            DistrictDetailsResource::collection($districts),
            'Job location lists'
        );
    }
}
