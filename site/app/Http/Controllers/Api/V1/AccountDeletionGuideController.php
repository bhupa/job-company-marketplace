<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ForceUpdate\AccountDeletionGuideResource;
use App\Services\V1\AccountDeletionGuideService;
use Illuminate\Http\Request;

class AccountDeletionGuideController extends BaseController
{
    protected $accountDeletionGuideService;

    public function __construct(
        AccountDeletionGuideService $accountDeletionGuideService
    ) {
        $this->accountDeletionGuideService = $accountDeletionGuideService;
    }

    public function index(Request $request)
    {
        $output = $this->accountDeletionGuideService->latest();
        $result = $output ? new AccountDeletionGuideResource($output) : null;

        return $this->success($result, 'Acccount deletion guide lists');
    }
}
