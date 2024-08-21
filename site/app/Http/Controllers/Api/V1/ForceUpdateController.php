<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ForceUpdate\ForceUpdateDetailsResource;
use App\Services\V1\ForceUpdateService;
use Illuminate\Http\Request;

class ForceUpdateController extends BaseController
{
    protected $forceUpdateService;

    public function __construct(ForceUpdateService $forceUpdateService)
    {
        $this->forceUpdateService = $forceUpdateService;
    }

    public function index(Request $request)
    {
        $output = $this->forceUpdateService->latest();
        $result = $output ? new ForceUpdateDetailsResource($output) : null;

        return $this->success($result, 'Force update details');
    }
}
