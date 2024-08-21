<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\ChatRoom;
use App\Services\FireBaseService;

class ChatRoomController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ChatRoom';
    protected $fireBaseService;
    public function __construct(FireBaseService $fireBaseService)
        {
        $this->fireBaseService = $fireBaseService;
        }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ChatRoom());
        $grid->model()->whereNotNull('matching_id');
        $grid->column('id', __('Id'));
        $grid->column('Jobseeker', __('Jobseeker'))->display(function () {
            // Assuming you have a 'type' column in the User model
            return $this->match->job_seeker_id
                ? $this->match->jobseeker->first_name .
                        ' ' .
                        $this->match->jobseeker->last_name
                : null;
        });
        $grid->column('company', __('Company'))->display(function () {
            // Assuming you have a 'type' column in the User model
            return $this->match->company_id
                ? $this->match->company->company_name
                : null;
        });
        $grid
            ->column('payment_type')
            ->select([
                0 => 'No Action',
                // 'iap' => 'Iap',
                // 'stripe' => 'Stripe',
                'admin' => 'Admin',
            ])
            ->setAttributes(['style' => '  visibility:visible;']);
        // $grid->column('payment_type', __('Payment Type'));
        // $grid->column('status')->bool(['1' => true, '0' => false]);
        $grid->column('admin_assist')->bool(['1' => true, '0' => false]);
        $grid->column('in_app_id', __('In app id'));
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');
        $grid->column('updated_at', __('Updated at'))->dateFormat('Y-m-d');
        $grid->disableActions();
        $grid->disableCreateButton();

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
        $show = new Show(ChatRoom::findOrFail($id));
        $show->field('id', __('Id'));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableList();
            $tools->disableDelete();
        });

        $show->chats('Chats Messages', function ($chat) {
            $chat->setResource('/admin/chats');

            $chat->id();
            $chat->message();
            $chat->sendBy('Sender', __('Name'))->display(function () {
                // Assuming you have a 'type' column in the User model
                if ($this->sendBy && $this->sendBy->user_type == 1) {
                    return $this->sendBy->jobseeker->first_name .
                        ' ' .
                        $this->sendBy->jobseeker->last_name;
                } elseif ($this->sendBy && $this->sendBy->user_type == 2) {
                    return $this->sendBy->company->company_name;
                } elseif ($this->sendBy == null) {
                    return null;
                }
            });
            $chat->created_at()->dateFormat('Y-m-d');
            $chat->actions(function ($actions) {
                $actions->disableEdit();
            });
            $chat->disableCreateButton();
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
        $form = new Form(new ChatRoom());

        $form->text('name', __('Name'));
        $form->image('image', __('Image'));
        $form->text('type', __('Type'));
        $form->switch('status', __('Status'))->default(1);
        $form->switch('admin_assist', __('Admin assist'));
        $form->number('matching_id', __('Matching id'));
        $form->number('user_id', __('User id'));
        $form->number('created_by', __('Created by'));
        $form->number('payment_id', __('Payment id'));
        $form->text('payment_type', __('Payment type'));
        $form->number('in_app_id', __('In app id'));

        $form->saving(function (Form $form) {
            $chatroom = $form->model();
            $payment_type = $form->input('payment_type');
           
          
            if ($payment_type == 'admin' && $chatroom->match ) {
                $company = $chatroom->match->company ? $chatroom->match->company->user : null;
                $jobseeker = $chatroom->match->jobseeker ? $chatroom->match->jobseeker->user : null;
                 $company->device_token &&
                    $this->fireBaseService->sendOtp(
                        $company,
                        'unrestricted-chat',
                        $chatroom
                    );


                $jobseeker->device_token &&
                    $this->fireBaseService->sendOtp(
                        $jobseeker,
                        'unrestricted-chat',
                        $chatroom
                    );
                }
            unset ($chatroom->url);

            });
        return $form;
    }
}
