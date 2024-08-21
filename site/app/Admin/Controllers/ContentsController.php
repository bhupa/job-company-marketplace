<?php

namespace App\Admin\Controllers;

use App\Models\V1\Content;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class ContentsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Content';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Content());

        $grid->column('id', __('Id'));
        $grid->column('title_en', __('Title en'));
        $grid->column('title_ja', __('Title ja'));
        $grid->column('type', __('Type'));
        $grid->column('description_en', __('Description en'))->limit(30);
        $grid->column('description_ja', __('Description ja'))->limit(30);
        // $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Content::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title_en', __('Title en'));
        $show->field('title_ja', __('Title ja'));
        $show->field('type', __('Type'));
        $show->field('description_en', __('Description en'));
        $show->field('description_ja', __('Description ja'));
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
        $form = new Form(new Content());

        $form->text('title_en', __('Title English'));
        $form->text('title_ja', __('Title Japanese'));
        $form->url('link', __('Link'));
        $form
            ->select('type', __('Type'))
            ->options([
                'chat_policy' => 'Chat policy',
                'job_policy' => 'Job policy',
                'privacy_policy' => 'Privacy policy',
                'terms_of_service' => 'Terms of service',
                'user_policy' => 'User policy',
            ])
            ->creationRules(['required', 'unique:contents,type'])
            ->updateRules(['required', 'unique:contents,type,{{id}}']);
        //    'terms_of_service_company'=>'Terms for service company' remove from type
        $form->textarea('description_en', __('Description English'));
        $form->textarea('description_ja', __('Description Japanese'));
        return $form;
    }
}
