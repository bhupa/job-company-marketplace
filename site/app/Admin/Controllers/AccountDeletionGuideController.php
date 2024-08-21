<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\AccountDeletionGuide;

class AccountDeletionGuideController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AccountDeletionGuide';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AccountDeletionGuide());

        $grid->column('en_content', __('En Content'))->limit(60);
        $grid->column('ja_content', __('Ja Content'))->limit(60);
        $grid->quickSearch(function ($model, $query) {
            return $model->where(function ($subquery) use ($query) {
                // Rename the parameter here
                $subquery
                    ->where('en_content', 'like', "%$query%")
                    ->orWhere('ja_content', 'like', "%$query%");
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
        $show = new Show(AccountDeletionGuide::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AccountDeletionGuide());
        $form
            ->textarea('en_content', __('En Content'))
            ->creationRules(['required'])
            ->updateRules(['required']);
        $form
            ->textarea('ja_content', __('Ja Content'))
            ->creationRules(['required'])
            ->updateRules(['required']);

        return $form;
    }
}
