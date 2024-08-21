<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\ForceUpdate;

class ForceUpdateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ForceUpdate';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ForceUpdate());

        $grid->column('ios_version', __('Ios Version'));
        $grid->column('ios_build_number', __('Ios Build Number'));
        $grid->column('android_version', __('Android Version'));
        $grid->column('android_build_number', __('Android Build Number'));
        $grid->quickSearch(function ($model, $query) {
            return $model->where(function ($subquery) use ($query) {
                // Rename the parameter here
                $subquery
                    ->where('ios_version', 'like', "%$query%")
                    ->orWhere('ios_build_number', 'like', "%$query%")
                    ->orWhere('android_version', 'like', "%$query%")
                    ->orWhere('android_build_number', 'like', "%$query%");
            });
        });
        $grid->disableFilter();
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
        $show = new Show(ForceUpdate::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ForceUpdate());
        $form
            ->text('ios_version', __('Ios Version'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        $form
            ->text('ios_build_number', __('Ios Build Number'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        $form
            ->text('android_version', __('Android Version'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        $form
            ->text('android_build_number', __('Android Build Number'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        return $form;
    }
}
