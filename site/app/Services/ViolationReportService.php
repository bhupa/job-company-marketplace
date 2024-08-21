<?php

namespace App\Services;

use App\Models\ViolationReports;

class ViolationReportService extends BaseService
{
    public function __construct(ViolationReports $violationReports)
    {
        $this->model = $violationReports;
    }

    public function create($request)
    {
        $data['created_by'] = auth()->id();
        $data['message'] = $request->message;
        $data['user_id'] = $request->user_id;
        $data['chat_room_id'] = $request->chat_room_id;

        return $this->model->create($data);
    }

    public function update($request, $violationReports)
    {
        $data = $request->except('_token');

        return tap($violationReports->update($data));
    }
}
