<?php

namespace App\Admin\Controllers;

use App\Models\User;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\ViolationReports;
use App\Services\FireBaseService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class ProfileViolationReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Profile Violation Report';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $fireBaseService, $userService;
    public function __construct(
        FireBaseService $fireBaseService,
        UserService $userService
    ) {
        $this->fireBaseService = $fireBaseService;
        $this->userService = $userService;
    }
    protected function grid()
    {
        $grid = new Grid(new ViolationReports());

        $grid
            ->model()
            ->whereNull('chat_room_id')
            ->whereHas('user')
            ->whereHas('createdBy');
        $grid->column('id', __('Id'));
        $grid->column('created_by', __('Report By'))->display(function () {
            // Assuming you have a 'type' column in the User model
            $type =
                $this->createdBy && $this->createdBy->user_type == 1
                    ? 'Jobseeker'
                    : 'Company';
            if ($this->createdBy && $this->createdBy->user_type == 1) {
                return $this->createdBy->jobseeker->first_name .
                    ' ' .
                    $this->createdBy->jobseeker->last_name .
                    '(  ' .
                    $type .
                    ' )';
            } elseif ($this->createdBy && $this->createdBy->user_type == 2) {
                return $this->createdBy->company->company_name .
                    '(  ' .
                    $type .
                    ' )';
            }
        });
        $grid->column('user_id', __('Report To'))->display(function () {
            // Assuming you have a 'type' column in the User model
            $type =
                $this->user && $this->user->user_type == 1
                    ? 'Jobseeker'
                    : 'Company';
            if ($this->user && $this->user->user_type == 1) {
                return $this->user->jobseeker->first_name .
                    ' ' .
                    $this->user->jobseeker->last_name .
                    '   (  ' .
                    $type .
                    ' )';
            } elseif ($this->user && $this->user->user_type == 2) {
                return $this->user->company->company_name .
                    '(  ' .
                    $type .
                    ' )';
            }
        });

        $grid
            ->column('status')
            ->select([0 => 'No Action', 1 => 'Violate', 2 => 'Not Violate'])
            ->setAttributes(['style' => '  visibility:visible;']);
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });
        $grid->quickSearch(function ($model, $query) {
            return $model->where(function ($subquery) use ($query) {
                $subquery->whereHas('createdBy', function ($subquery) use (
                    $query
                ) {
                    $subquery->where(function ($innerSubquery) use ($query) {
                        $innerSubquery
                            ->whereHas('jobseeker', function ($subquery) use (
                                $query
                            ) {
                                $subquery->whereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    ["%$query%"]
                                );
                            })
                            ->orWhereHas('company', function ($subquery) use (
                                $query
                            ) {
                                $subquery->where(
                                    'company_name',
                                    'like',
                                    "%$query%"
                                );
                            });
                    });
                });

                $subquery->orWhereHas('user', function ($subquery) use (
                    $query
                ) {
                    $subquery->where(function ($subquery) use ($query) {
                        $subquery
                            ->whereHas('jobseeker', function ($subquery) use (
                                $query
                            ) {
                                $subquery->whereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    ["%$query%"]
                                );
                            })
                            ->orWhereHas('company', function ($subquery) use (
                                $query
                            ) {
                                $subquery->where(
                                    'company_name',
                                    'like',
                                    "%$query%"
                                );
                            });
                    });
                });
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $violation = ViolationReports::with([
            'user',
            'createdBy',
            'user.jobseeker',
            'user.company',
        ])->find($id);
        $show = new Show(User::findOrFail($violation->user_id));
        $show->field('full_name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('country_code', __('Country code'));
        $show->field('phone', __('Phone'));
        $show
            ->field('user_type', __('User type'))
            ->using(['1' => 'Jobseeker', '2' => 'Company']);
        $show->field('status', __('Status'))->using([
            '0' => 'pending',
            '1' => 'active',
            '2' => 'deactivated',
            '3' => 'blocked',
        ]);
        $show->violation('violation.message', __('Message'));
        $show->field('violation', __('Message'))->as(function ($violation) {
            return $violation->message;
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ViolationReports());
        $form->select('status');
        $form->saving(function (Form $form) {
            $violation = $form->model();
            $user = $violation->createdBy;
            $violateUser = $violation->user;
            $status = $form->input('status');
            $status == 1 ? $this->userService->violationUser($violateUser) : '';
            if ($user->device_token) {
                $status == 1
                    ? $this->fireBaseService->sendOtp(
                        $user,
                        'violation-approved',
                        $violation
                    )
                    : $this->fireBaseService->sendOtp(
                        $user,
                        'violation-reject',
                        $violation
                    );
            }
        });

        return $form;
    }
}
