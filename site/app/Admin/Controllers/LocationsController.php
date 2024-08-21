<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Locations;

class LocationsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Locations';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Locations());

        $grid->column('id', __('Id'));
        $grid->column('jp_name', __('Jp name'));
        $grid->column('en_name', __('En name'));

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
        $show = new Show(Locations::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('jp_name', __('Jp name'));
        $show->field('en_name', __('En name'));
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
        $form = new Form(new Locations());

        $form->text('jp_name', __('Jp name'));
        $form->text('en_name', __('En name'));

        return $form;
    }
}
