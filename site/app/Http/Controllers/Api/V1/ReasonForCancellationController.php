<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\ReasonForCancellation\ReasonForCancellationStore;
use App\Http\Resources\V1\ReasonForCancellation\ReasonForCancellationResource;
use App\Services\UserService;
use App\Services\V1\ReasonForCancellationService;


class ReasonForCancellationController extends BaseController
    {
    //
    protected $reasonForCancellation;
    protected $userservice;

    public function __construct(
        ReasonForCancellationService $reasonForCancellation,
        UserService $userservice
    ) {
        $this->reasonForCancellation = $reasonForCancellation;
        $this->userservice = $userservice;
        }

    public function index()
        {
        $lang = getUserLanguage(auth()->user());
        $data = getReasonsForCancellation();

        return $this->success($data, 'Account cancellation form');

        }



    public function store(ReasonForCancellationStore $request)

        {

        $user = auth()->user();
        $data = $request->except('_token');
        $data['full_name'] = auth()->user()->fullName;
        $data['email'] = auth()->user()->email;
        $data['phone'] = auth()->user()->country_code . '-' . auth()->user()->phone;
        $data['user_type'] = auth()->user()->user_type;
        $output = $this->reasonForCancellation->create($data);

        $this->userservice->deleteUserAccount($user);
        return $this->success(
            new ReasonForCancellationResource($output),
            'User cancellation details'
        );
        }

    public function show()
        {
        $details = getReasonsForCancellation();

        $data = null;
        if (auth()->user()->cancellationReason) {
            $data = [
                'reason' => getCancellationReasonTitle(
                    $details,
                    auth()->user()->cancellationReason->reason
                ),
                'subReason' => auth()->user()->cancellationReason->sub_reason
                    ? getCancellationSubReasonTitle(
                        $details,
                        auth()->user()->cancellationReason->sub_reason
                    )
                    : null,
                'futurePlan' => auth()->user()->cancellationReason->future_plan
                    ? getCancellationFuturePlanReasonTitle(
                        $details,
                        auth()->user()->cancellationReason->future_plan
                    )
                    : null,
            ];
            }

        return $this->success($data, 'User cancellation details');
        }
    }
