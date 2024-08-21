<?php

namespace App\Services\V1;

use App\Models\V1\ForceUpdate;
use App\Services\BaseService;

class ForceUpdateService extends BaseService
{
    protected $forceUpdate;

    public function __construct(ForceUpdate $forceUpdate)
    {
        $this->model = $forceUpdate;
    }
}
