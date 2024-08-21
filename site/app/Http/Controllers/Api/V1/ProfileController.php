<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserService;

class ProfileController extends BaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function proilePercentage()
    {
        $percentage = $this->userService->profilePercentage();

        return $this->success(
            ['percentage' => round($percentage, 2)],
            'User profile percentage'
        );
    }
}
