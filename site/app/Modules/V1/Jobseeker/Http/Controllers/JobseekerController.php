<?php

namespace App\Modules\V1\Jobseeker\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\V1\Jobseeker\Http\Resources\JobseekerPaginationResource;
use App\Modules\V1\Jobseeker\Services\JobseekerService;
use App\Services\ChatService;
use App\Services\ImageFileService;
use App\Services\V1\TagService;
use Illuminate\Http\Request;

class JobseekerController extends Controller
{
    protected $jobseekerservice;

    protected $imagefileservice;
    protected $chat;
    protected $tagService;

    // in this controller we have call the jobseekerService where all the model working will be perfrom on this service
    public function __construct(
        JobseekerService $jobseekerservice,
        ImageFileService $imagefileservice,
        ChatService $chat,
        TagService $tagService
    ) {
        $this->jobseekerservice = $jobseekerservice;
        $this->imagefileservice = $imagefileservice;
        $this->chat = $chat;
        $this->tagService = $tagService;
    }

    //  it is used to fetch all the jobseeker lists
    public function index(Request $request)
    {
        $request->has('type')
            && $request->has('type') == 'reset'
            && resetRedisData('jobseeker', auth()->id());

        $jobseekers = $this->jobseekerservice->filter($request);
        $matRequestend = $this->jobseekerservice->countRequest();
        $countFavouritend = $matRequestend['countFavourite'];
        $userRequestend = $matRequestend['userRequest'];
        $data = [
            'favouriteCount' => $countFavouritend ? $countFavouritend : 0,
            'dailyCount' => $userRequestend ? $userRequestend : 0,
            'chatRequestCount' => $this->chat->directChatCount(),
            'dailylimit' => 10,
            'favoriteLimit' => 10,
            'chatRequestLimit' => 1,
            'items' => new JobseekerPaginationResource($jobseekers),
        ];

        return $this->success($data, 'Jobseeker lists');
    }

    // this function will create the jobseeker
    public function store(JobseekerStoreRequest $request)
    {
        // this will check the user type jobseeker to give access
        $this->middleware('jobseeker');
        try {
            if (auth()->user()->jobseeker) {
                return $this->success(
                    new JobseekerDetailsResource(auth()->user()->jobseeker),
                    'Jobseeker account already exists'
                );
            }

            $output = $this->jobseekerservice->create($request);

            if ($request->has('tags')) {
                $this->tagService->create($output, $request->tags);
            }
            if ($request->hasFile('intro_video')) {
                $video = $this->jobseekerservice->uploadImg(
                    $request->intro_video,
                    'intro_video'
                );
                auth()
                    ->user()
                    ->update(['intro_video' => $video]);
            }

            $id = $output->id;
            $model = 'App\Models\Jobseeker';
            if ($request->hasFile('image')) {
                $this->imagefileservice->create(
                    $id,
                    $request->image,
                    $model,
                    'jobseeker'
                );
            }

            return $this->success(
                new JobseekerDetailsResource($output),
                'Jobseeker created successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // this function will update the jobseeker
    public function update(
        JobseekerUpdateRequest $request,
        Jobseeker $jobseeker
    ) {
        // this will check the user type jobseeker to give access
        $this->middleware('jobseeker');
        try {
            $this->jobseekerservice->update($request, $jobseeker);
            $id = $jobseeker->id;
            $model = 'App\Models\Jobseeker';

            if (!$request->hasFile('profile_img') && !$request->hasFile('image') && !$request->has('image_ids')) {
                if ($request->has('tags')) {
                    $this->tagService->create($jobseeker, $request->tags);
                } else {
                    $jobseeker->tags()->exists() && $jobseeker->tags()->detach();
                }
                if ($request->hasFile('intro_video')) {
                    auth()->user()->intro_video
                        && $this->jobseekerservice->deleteImage(auth()->user()->intro_video);

                    $video = $this->jobseekerservice->uploadImg(
                        $request->intro_video,
                        'intro_video'
                    );

                    $jobseeker->user->update(['intro_video' => $video]);
                }
                if ($request->isIntroVideoDeleted == 'true') {
                    $this->jobseekerservice->deleteImage($jobseeker->user->intro_video);
                    $jobseeker->user->update(['intro_video' => null]);
                }
            } else {
                if ($request->hasFile('image')) {
                    $this->imagefileservice->create(
                        $id,
                        $request->image,
                        $model,
                        'jobseeker'
                    );
                }
                if ($request->has('image_ids')) {
                    $this->imagefileservice->deleteBulkImage($request->image_ids);
                }
            }

            $output = $this->jobseekerservice->find($jobseeker->id);

            return $this->success(
                new JobseekerDetailsResource($output),
                'Jobseeker updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used to get the details of the jobseeker
    public function show(Jobseeker $jobseeker)
    {
        try {
            return $this->success(
                new JobseekerDetailsResource($jobseeker),
                'Jobseeker details'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used to get the delete  jobseeker
    public function destroy(Jobseeker $jobseeker)
    {
        $this->middleware('jobseeker'); // this will check the user type jobseeker to give access
        try {
            $this->jobseekerservice->deleteImage($jobseeker->image);
            $jobseeker->delete();

            return $this->success([], 'Jobseeker deleted successfully');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // this function is used to get the jobseeker details of login jobseeker
    public function getJobSeekerDetails()
    {
        $jobseeker = Jobseeker::where('user_id', auth()->id())->first();
        if ($jobseeker) {
            return $this->success(
                new JobseekerDetailsResource($jobseeker),
                'Jobseeker details'
            );
        }

        return $this->errors(['message' => 'Jobseeker not found'], 400);
    }
}
