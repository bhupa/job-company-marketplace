<?php

namespace App\Http\Controllers\Api\V1\Matching;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Matching\UnRewindRequest;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Models\Jobs;
use App\Models\Jobseeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RewindController extends BaseController
{
    protected $jobseeker;
    protected $job;
    //
    public function __construct(Jobseeker $jobseeker, Jobs $job)
    {
        $this->jobseeker = $jobseeker;
        $this->job = $job;
    }
    public function unRewind(UnRewindRequest $request)
    {
        try {
            $input =
                auth()->user()->user_type == 1
                    ? $request->job_id
                    : $request->job_seeker_id;
            $output =
                auth()->user()->user_type == 1
                    ? getLeftSwipeJobs()
                    : getLeftSwipeJobseekers();
            $key =
                auth()->user()->user_type == 1
                    ? 'jobs_' . auth()->id()
                    : 'jobseeker_' . auth()->id();
           
            $result =
                auth()->user()->user_type == 1
                    ? new JobDetailsResource($this->job->find($request->job_id))
                    : new JobseekerDetailsResource(
                        $this->jobseeker->find($request->job_seeker_id)
                    );
            if (is_array($output)) {
                foreach ($output as $value) {
                    if ($value === $input) {
                        // Delete the key from Redis
                       (auth()->user()->user_type == 1) ?  removeLeftSwipeJobs($input) : removeFromLeftSwipeJobseekers($input);
                    }
                }
                $message = 'Rewind done successfully';
            } else {
                $message = 'Data not found in rewind lists';
            }
            return $this->success($result, $message);
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }
}
