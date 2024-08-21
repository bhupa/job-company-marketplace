<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\Tag;

class TagController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tag';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tag());

        $grid->quickSearch(function ($model, $query) {
            $model
                ->where('name', 'like', "%{$query}%")
                ->orWhere('ja_name', 'like', "%{$query}%");
        });
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        // $grid->column('status', __('Status'));
        $grid->column('type', __('Type'));
        $grid->column('ja_name', __('Ja name'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Tag::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('status', __('Status'));
        $show->field('type', __('Type'));
        $show->field('ja_name', __('Ja name'));
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
        $form = new Form(new Tag());

        $form->text('name', __('Name'));
        // $form->number('status', __('Status'));
        $form
            ->select('type', __('Type'))
            ->options(['jobseeker' => 'Jobseeker', 'job' => 'Job']);
        $form->text('ja_name', __('Ja name'));
        return $form;
    }
}
