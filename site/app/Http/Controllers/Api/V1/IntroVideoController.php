<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IntroVideo\IntroVideoStoreRequest;
use App\Http\Resources\V1\User\UserDetailsResources;
use App\Services\UserService;

class IntroVideoController extends BaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(IntroVideoStoreRequest $request)
    {
        if (auth()->user()->intro_video) {
            $this->userService->deleteImage(auth()->user()->intro_video);
        }
        $video = $this->userService->uploadImg(
            $request->intro_video,
            'intro_video'
        );
        auth()
            ->user()
            ->update(['intro_video' => $video]);

        return $this->success(
            new UserDetailsResources(auth()->user()),
            'Video uploaded successfully'
        );
    }
}
