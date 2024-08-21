<?php

namespace App\Modules\V1\Company\Http\Controllers;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Models\Company;
use App\Modules\V1\Company\Http\Requests\CompanyStoreRequest;
use App\Modules\V1\Company\Http\Requests\CompanyUpdateRequest;
use App\Modules\V1\Company\Http\Resources\CompanyDetailsResource;
use App\Modules\V1\Company\Http\Resources\CompanyPaginationResource;
use App\Modules\V1\Company\Services\CompanyService;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use App\Services\ImageFileService;
use App\Services\JobService;
use App\Services\V1\JobLocationService;
use App\Services\V1\TagService;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{

    protected $companyservice;
    protected $imagefileservice;
    protected $jobservice;
    protected $jobLocationService;
    protected $tagService;

    // in this controller we have call the CompanyService,ImageFileService  where all the model working will be perfrom on this service
    public function __construct(
        CompanyService $companyservice,
        ImageFileService $imagefileservice,
        JobService $jobservice,
        JobLocationService $jobLocationService,
        TagService $tagService
    ) {
        $this->companyservice = $companyservice;
        $this->imagefileservice = $imagefileservice;
        $this->jobservice = $jobservice;
        $this->jobLocationService = $jobLocationService;
        $this->tagService = $tagService;
        }

    // this function will fetch all the lists of the company
    public function index(Request $request)
        {
        $jobseekers = $this->companyservice->filter($request);

        return $this->success(
            new CompanyPaginationResource($jobseekers),
            'Job lists'
        );
        }

    // it is used to create the company
    public function store(CompanyStoreRequest $request)
        {
        $this->middleware('company_owner'); // this will check if user is employer to create company
        try {
            if (empty(auth()->user()->company)) {
                $company = $this->companyservice->create($request);
                $id = $company->id;
                $model = 'App\Models\Company';
                if ($request->hasFile('image')) {
                    $this->imagefileservice->create(
                        $id,
                        $request->image,
                        $model,
                        'company'
                    );
                    }
                if ($request->hasFile('intro_video')) {
                    $video = $this->companyservice->uploadImg(
                        $request->intro_video,
                        'intro_video'
                    );
                    auth()
                        ->user()
                        ->update(['intro_video' => $video]);
                    }

                // first company will create after that job will create but compnay is not save in data base
                $job = $this->jobservice->create($request);
                $jobId = $job->id;
                $jobModel = 'App\Models\Jobs';
                if ($request->hasFile('job_image')) {
                    $this->imagefileservice->create(
                        $jobId,
                        $request->job_image,
                        $jobModel,
                        'job'
                    );
                    }
                if ($request->has('job.job_location')) {
                    $this->jobLocationService->create(
                        $job,
                        $request->job['job_location']
                    );
                    }
                if ($request->has('tags')) {
                    $this->tagService->create($job, $request->tags);
                    }
                $output = [
                    'id' => $company->id,
                    'companyName' => $company->company_name,
                    'aboutCompany' => $company->about_company,
                    'address' => $company->address,
                    'status' => $company->status,
                    'logo' => url('/') . '/storage/' . $company->logo,
                    'image' => ImageFileResource::collection($company->images),
                    'user' => new UserDetailsResource($company->user),
                    'job' => new JobDetailsResource($job),
                ];

                return $this->success($output, 'Company added successfully');
                }

            return $this->success(
                new CompanyDetailsResource(auth()->user()->company),
                'Company already created'
            );
            } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    // it is used for the company update
    public function update(CompanyUpdateRequest $request, Company $company)
        {
        $this->middleware('company_owner'); // this will check if user is employer to update company
        try {
            $this->companyservice->update($request, $company);
            $id = $company->id;
            $model = 'App\Models\Company';

            if ($request->hasFile('image')) {
                $this->imagefileservice->create(
                    $id,
                    $request->image,
                    $model,
                    'company'
                );
                }
            if ($request->hasFile('intro_video')) {
                auth()->user()->intro_video
                    && $this->companyservice->deleteImage(auth()->user()->intro_video);
                $video = $this->companyservice->uploadImg(
                    $request->intro_video,
                    'intro_video'
                );

                $company->user->update(['intro_video' => $video]);
                }
            if ($request->isIntroVideoDeleted == 'true') {
                $this->companyservice->deleteImage($company->user->intro_video);
                $company->user->update(['intro_video' => null]);
                }

            if ($request->has('image_ids')) {
                $this->imagefileservice->deleteBulkImage($request->image_ids);
                }

            $output = $this->companyservice->find($company->id);

            return $this->success(
                new CompanyDetailsResource($output),
                'Company updated successfully'
            );
            } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    // it is used fto fetch the company details
    public function show(Company $company)
        {
        try {
            return $this->success(
                new CompanyDetailsResource($company),
                'Company details'
            );
            } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    // it is used to delete the destroy
    public function destroy(Company $company)
        {
        $this->middleware('company_owner'); // this will check if user is employer to delete company
        try {
            $company->delete();

            return $this->success([], 'Company deleted successfully');
            } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    // this function is used to get the compnay details for the login user
    public function getCompanyDetails()
        {
        $company = Company::where('user_id', auth()->id())->first();
        if ($company) {
            return $this->success(
                new CompanyDetailsResource($company),
                'Company details'
            );
            }

        return $this->errors(['message' => 'Company not found'], 400);
        }

    public function checkName(Request $request)
        {
        $company = Company::where(
            'company_name',
            $request->company_name
        )->first();

        if ($company) {
            return $this->errors(
                ['message' => $company->company_name . ' already exists'],
                400
            );
            }

        return $this->success([], $request->company_name . ' not exists');
        }
    // Define your controller methods here
}
