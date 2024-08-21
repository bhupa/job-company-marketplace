<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Job\JobCategoryResource;
use App\Models\JobCategory;

class CategoryController extends BaseController
{
    // this function will fetch all the categories
    public function index()
    {
        $categories = JobCategory::orderBy('name')->get();

        return $this->success(
            JobCategoryResource::collection($categories),
            'Job category lists'
        );
    }
}
