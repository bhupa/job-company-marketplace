<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\District;

class DistrictController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'District';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new District());

        $grid->quickSearch(function ($model, $query) {
            $model
                ->where('name', $query)
                ->orWhere('ja_name', 'like', "%{$query}%");
        });
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('ja_name', __('Ja name'));
        $grid->column('parent_id', __('Parent'))->display(function () {
            return $this->parent
                ? $this->parent->name . '(' . $this->parent->ja_name . ')'
                : null;
        });
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
        $show = new Show(District::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('ja_name', __('Ja name'));
        $show->field('parent_id', __('Parent id'));
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
        $form = new Form(new District());

        $form->text('name', __('Name'));
        // $form->number('status', __('Status'));
        // $form->number('order', __('Order'));
        $form->text('ja_name', __('Ja name'));
        // $form->number('parent_id', __('Parent id'));
        $form
            ->select('parent_id', __('Parent'))
            ->options(function () use ($form) {
                $districts = District::whereNull('parent_id')
                    ->get()
                    ->map(function ($district) {
                        return [
                            $district->id =>
                                $district->name .
                                ' (' .
                                $district->ja_name .
                                ')',
                        ];
                    })
                    ->flatten()
                    ->all();

                // Get the current value of parent_id
                $selectedDistrictId = $form->model()->parent_id;

                // Add a default option
                $options = $form->model()->parent_id
                    ? [
                        $selectedDistrictId =>
                            $form->model()->parent->name .
                            ' (' .
                            $form->model()->parent->ja_name .
                            ')',
                    ]
                    : null;
                // Merge the existing districts with the default option
                return $options ? $options + $districts : $districts;
            })
            ->default($form->model()->parent_id);

        return $form;
    }
}
