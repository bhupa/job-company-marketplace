<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\JobCategory;

class JobCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'JobCategory';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JobCategory());
        $grid->quickSearch(function ($model, $query) {
            $model->where('name', 'like', "%{$query}%");
        });
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('jp_name', __('Name Ja'));
        // $grid->column('status', __('Status'));
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
        $show = new Show(JobCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('jp_name', __('Name Ja'));
        // $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'))->dateFormat('Y-m-d');
        $show->field('updated_at', __('Updated at'))->dateFormat('Y-m-d');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new JobCategory());

        $form
            ->text('name', __('Name'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        $form
            ->text('jp_name', __('Name Ja'))
            ->creationRules(['required'])
            ->updateRules(['required']);

        return $form;
    }
}
