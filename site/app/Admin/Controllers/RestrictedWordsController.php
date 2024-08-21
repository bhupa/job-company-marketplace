<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\RestrictedWords;

class RestrictedWordsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'RestrictedWords';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RestrictedWords());

        $grid->column('word', __('Word'));
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');
        $grid->column('updated_at', __('Updated at'))->dateFormat('Y-m-d');
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
        $show = new Show(RestrictedWords::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RestrictedWords());

        $form
            ->text('word', __('Word'))
            ->creationRules(['required', 'unique:restricted_words,word'])
            ->updateRules(['required', 'unique:restricted_words,word,{{id}}']);
        return $form;
    }
}
