<?php

namespace App\Services\V1;

use App\Models\V1\AccountDeletionGuide;
use App\Services\BaseService;

class AccountDeletionGuideService extends BaseService
{
    protected $accountDeletionGuide;

    public function __construct(AccountDeletionGuide $accountDeletionGuide)
    {
        $this->model = $accountDeletionGuide;
    }
}
