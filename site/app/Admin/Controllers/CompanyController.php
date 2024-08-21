<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CompanyDelete;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Company;

class CompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Company';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Company());
        $grid->quickSearch(function ($model, $query) {
            $model
                ->where('company_name', $query)
                ->orWhere('company_name', 'like', "%{$query}%");
        });
        $grid->column('id', __('Id'));
        $grid->column('company_name', __('Company name'));
        $grid->column('about_company', __('About company'))->limit(60);
        $grid->column('address', __('Address'));
        $grid
            ->column('logo', __('Logo'))
            ->image(url('/') . '/storage', 100, 100);
        $grid->column('status', __('Status'));
        $grid->column('user.email', __('User'));
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');
        $grid->column('updated_at', __('Updated at'))->dateFormat('Y-m-d');
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->add(new CompanyDelete());
        });
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
        $show = new Show(Company::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('company_name', __('Company name'));
        $show->field('about_company', __('About company'));
        $show->field('address', __('Address'));
        $show
            ->field('logo', __('Logo'))
            ->image(url('/') . '/storage/', 100, 100);
        $show->field('status', __('Status'));
        $show->field('user_id', __('User id'));
        $show->user('user', function ($author) {
            $author->id();
            $author->name();
            $author->email();
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
        $form = new Form(new Company());

        $form->text('company_name', __('Company name'));
        $form->textarea('about_company', __('About company'));
        $form->text('address', __('Address'));
        $form->image('logo', __('Logo'))->move('/company');
        $form->number('status', __('Status'));
        $form->number('user_id', __('User id'));

        return $form;
    }
}
