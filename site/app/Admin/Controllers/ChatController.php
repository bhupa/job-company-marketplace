<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Chat;

class ChatController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chat';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Chat());
        $grid->model()->whereHas('sendBy');
        $grid->model()->orderBy('created_at', 'desc');
        $grid->column('id', __('Id'));
        $grid->column('file', __('File'));
        $grid->column('seen', __('Seen'));
        $grid->column('message', __('Message'));
        $grid->column('full_name')->display(function () {
            return $this->sendBy->fullName;
        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });
        // $grid->column('payment_id', __('Payment id'));
        // $grid->column('admin_id', __('Admin id'));

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
        $show = new Show(Chat::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('file', __('File'))->display(function ($file) {
            return '<a href="' .
                url('') .
                '/storage/' .
                $file .
                '" target="_blank">' .
                basename($file) .
                '</a>';
        });
        $show->field('seen', __('Seen'));
        $show->field('message', __('Message'));

        $show
            ->field('sendBy', __('Sent By'))
            ->as(function ($sendBy) {
                return $this->sendBy->fullName;
            })
            ->panel();
        // $show->field('send_by', __('Send by'));
        $show->field('payment_id', __('Payment id'));
        $show->field('chat_room_id', __('Chat room id'));
        $show->field('admin_id', __('Admin id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Chat());

        $form->file('file', __('File'));
        $form->datetime('seen', __('Seen'))->default(date('Y-m-d H:i:s'));
        $form->textarea('message', __('Message'));
        $form->number('send_by', __('Send by'));
        $form->number('payment_id', __('Payment id'));
        $form->number('chat_room_id', __('Chat room id'));
        $form->number('admin_id', __('Admin id'));

        return $form;
    }
}
