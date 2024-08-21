<?php

namespace App\Services\V1;

use App\Models\Matching;
use App\Services\BaseService;

class GlobalEventService extends BaseService
{
    public function count()
    {
        $column =
            auth()->user()->user_type == 1 ? 'job_seeker_id' : 'company_id';
        $value =
            auth()->user()->user_type == 1
                ? auth()->user()->jobseeker->id
                : auth()->user()->company->id;

        return Matching::where('created_by', '!=', auth()->user()->id)
            ->where($column, $value)
            ->whereNull('matched')
            ->whereNull('unmatched')
            ->whereNull('favourite_by')
            ->whereNotNull('company_id')
            ->whereNotNull('job_seeker_id')
            ->count();
    }
}
