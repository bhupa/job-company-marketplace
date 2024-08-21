<?php

namespace App\Admin\Controllers;

use App\Models\Chat;
use App\Models\ChatRoom;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\ViolationReports;
use App\Services\ChatService;
use App\Services\FireBaseService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class ChatViolationReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chat Violation Reports';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $fireBaseService, $userService, $chatService;
    public function __construct(
        FireBaseService $fireBaseService,
        UserService $userService,
        ChatService $chatService
    ) {
        $this->fireBaseService = $fireBaseService;
        $this->userService = $userService;
        $this->chatService = $chatService;
    }
    protected function grid()
    {
        $grid = new Grid(new ViolationReports());
        $grid
            ->model()
            ->whereNull('user_id')
            ->whereHas('chatroom.createdBy')
            ->whereHas('chatroom.match')
            ->whereHas('chatroom.match.jobseeker')
            ->whereHas('chatroom.match.company')
            ->whereHas('createdBy');
        $grid->filter(function ($filter) {
            $filter->where(function ($query) {
                $query->whereNotNull('created_by')->whereNotNull('chat_id');
            }, 'Custom Filter');
        });

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

        $grid
            ->column('status')
            ->select([0 => 'No Action', 1 => 'Violate', 2 => 'Not Violate'])
            ->setAttributes(['style' => '  visibility:visible;']);
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');

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
        $chats = Chatroom::whereHas('violation', function ($query) use ($id) {
            $query->where('id', $id);
        })->first();

        $show = new Show($chats);
        $show->field('match.company', __('Company'))->as(function ($company) {
            return $company->company_name;
        });
        $show
            ->field('match.jobseeker', __('Jobseeker'))
            ->as(function ($jobseeker) {
                return $jobseeker->first_name . ' ' . $jobseeker->first_name;
            });
        $show->field('violation', __('Message'))->as(function ($violation) {
            return $violation->message;
        });
        $show->chats('Chats', function ($chat) {
            $chat->id();

            $chat->seen();
            $chat->message();
            $chat->payment_id();
            $chat->admin_id();
            $chat->created_at()->dateFormat('Y-m-d');

            $chat->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableShow();
            });
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
        $form->switch('status');
        $form->saving(function (Form $form) {
            $violation = $form->model();
            $user = $violation->createdBy;
            $violateUser = Chat::where('chat_room_id',$violation->chat_room_id)->where('send_by','!=',$violation->created_by)->latest()->first()->sendBy;
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
