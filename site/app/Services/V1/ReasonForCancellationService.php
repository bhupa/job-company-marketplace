<?php

namespace App\Services\V1;

use App\Models\V1\ReasonForCancellation;
use App\Services\BaseService;

class ReasonForCancellationService extends BaseService
{
    protected $reasonForCancellation;

    public function __construct(ReasonForCancellation $reasonForCancellation)
    {
        $this->model = $reasonForCancellation;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $result)
    {
        $this->model->where('id', $id)->update($result);

        return $this->model->find($id);
    }
}
