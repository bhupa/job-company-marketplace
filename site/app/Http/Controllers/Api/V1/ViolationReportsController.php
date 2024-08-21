<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\ViolationReport\ViolationReportStoreRequest;
use App\Http\Resources\V1\ViolationReport\ViolationReportDetailsResource;
use App\Models\ViolationReports;
use App\Services\ViolationReportService;

class ViolationReportsController extends BaseController
{
    protected $violationReportService;

    public function __construct(ViolationReportService $violationReportService)
    {
        $this->violationReportService = $violationReportService;
    }

    public function store(ViolationReportStoreRequest $request)
    {
        try {
            $output = $this->violationReportService->create($request);

            return $this->success(
                new ViolationReportDetailsResource($output),
                'Voilation report has been created successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used for update job
    public function update(
        ViolationReportStoreRequest $request,
        ViolationReports $violationReports
    ) {
        try {
            $this->violationReportService->update($request, $violationReports);
            $output = $this->violationReportService->find(
                $violationReports->id
            );

            return $this->success(
                new ViolationReportDetailsResource($output),
                'Voilation report has been updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used to fetch all the details of the job
    public function show(ViolationReports $violationReports)
    {
        try {
            return $this->success(
                new ViolationReportDetailsResource($violationReports),
                'Voilation report details'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // it is used for delete job
    public function destroy(ViolationReports $violationReports)
    {
        try {
            $violationReports->delete();

            return $this->success(
                [],
                'Violation report has been deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }
}
