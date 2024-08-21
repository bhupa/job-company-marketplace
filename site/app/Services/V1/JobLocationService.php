<?php

namespace App\Services\V1;

use App\Services\BaseService;

class JobLocationService extends BaseService
{
    public function create($job, $joblocation)
    {
        $locations = getPrefectureDistricts($joblocation);

        return $job->locations()->sync($locations);
    }
}
