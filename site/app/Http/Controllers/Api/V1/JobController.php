<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Job\JobStoreRequest;
use App\Http\Requests\V1\Job\JobUpdateRequest;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Job\JobPaginationResource;
use App\Models\Jobs;
use App\Services\ChatService;
use App\Services\ImageFileService;
use App\Services\JobService;
use App\Services\V1\JobLocationService;
use App\Services\V1\TagService;
use Illuminate\Http\Request;

class JobController extends BaseController
{
    protected $job;
    protected $chat;
    protected $imagefileservice;
    protected $jobLocationService;
    protected $tagService;

    // in this controller we have call the Jobservice where all the model working will be perfrom on this service
    public function __construct(
        JobService $job,
        ChatService $chat,
        ImageFileService $imagefileservice,
        JobLocationService $jobLocationService,
        TagService $tagService
    ) {
        $this->job = $job;
        $this->chat = $chat;
        $this->imagefileservice = $imagefileservice;
        $this->jobLocationService = $jobLocationService;
        $this->tagService = $tagService;
    }

    // it is used to fetch all the lists of  job
    public function index(Request $request)
    {
        $request->has('type')
            && $request->has('type') == 'reset'
            && resetRedisData('jobs', auth()->id());
        $jobs = $this->job->filter($request);
        $matRequestend = $this->job->countRequest();
        $countFavouritend = $matRequestend['countFavourite'];
        $userRequestend = $matRequestend['userRequest'];
        $data = [
            'favouriteCount' => $countFavouritend ? $countFavouritend : 0,
            'dailyCount' => $userRequestend ? $userRequestend : 0,
            'chatRequestCount' => $this->chat->directChatCount(),
            'dailylimit' => 10,
            'favoriteLimit' => 10,
            'chatRequestLimit' => 1,
            'items' => new JobPaginationResource($jobs),
        ];

        return $this->success($data, 'Job lists');
    }

    // it is used to store the job
    public function store(JobStoreRequest $request)
    {
        $this->middleware('company_owner'); // this will check the user type employer to give access
        try {
            $job = $this->job->create($request);
            $id = $job->id;
            $model = 'App\Models\Jobs';
            if ($request->hasFile('job_image')) {
                $this->imagefileservice->create(
                    $id,
                    $request->job_image,
                    $model,
                    'job'
                );
            }
            if ($request->has('job_location')) {
                $this->jobLocationService->create($job, $request->job_location);
            }
            if ($request->has('tags')) {
                $this->tagService->create($job, $request->tags);
            }

            return $this->success(
                new JobDetailsResource($job),
                'Job has been created successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used for update job
    public function update(JobUpdateRequest $request, Jobs $jobs)
    {
        $this->middleware('company_owner'); // this will check the user type employer to give access
        try {
            $this->job->update($request, $jobs);
            $job = $this->job->find($jobs->id);
            $id = $job->id;
            $model = 'App\Models\Jobs';
            if ($request->hasFile('job_image')) {
                $job->image()->exists()
                    && $this->imagefileservice->deleteBulkImage([$job->image->id]);

                $this->imagefileservice->create(
                    $id,
                    $request->job_image,
                    $model,
                    'job'
                );
            }
            if ($request->has('job_location')) {
                $this->jobLocationService->create($job, $request->job_location);
            }
            if ($request->has('tags')) {
                $this->tagService->create($job, $request->tags);
            } else {
                $job->tags()->exists() && $job->tags()->detach();
            }
            $job->refresh();

            return $this->success(
                new JobDetailsResource($job),
                'Job has been updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used to fetch all the details of the job
    public function show(Jobs $jobs)
    {
        try {
            return $this->success(new JobDetailsResource($jobs), 'Job details');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used for delete job
    public function destroy(Jobs $jobs)
    {
        $this->middleware('company_owner'); // this will check the user type employer to give access
        try {
            $jobs->delete();

            return $this->success([], 'Jobs has been deleted successfully');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used for the fetch the jobas that match with jobseeker
    public function match(Request $request)
    {
        $this->middleware('jobseeker'); // this will check the user type employer to give access
        try {
            $jobseekers = $this->job->match($request);

            return $this->success(
                JobDetailsResource::collection($jobseekers),
                'Jobseeker matching job lists'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function lists(Request $request)
    {
        $jobs = $this->job->getLists($request);

        return $this->success(new JobPaginationResource($jobs), 'Job lists');
    }
}
