<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\ReasonForCancellation;

class ReasonForCancellationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ReasonForCancellation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReasonForCancellation());

        $grid->column('id', __('Id'));
        $grid->column('full_name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('user_type', 'UserType')->display(function () {
           return ($this->user_type ==1) ? 'jobseeker' : 'company';
        });
        $grid->column('reason', 'Reason')->display(function () {
            $details = getReasonsForCancellation();
            $title = getCancellationReasonTitle($details, $this->reason);
            return $title['en'];
        });
        $grid->column('sub_reason', __('Sub Reason'))->display(function () {
            $details = getReasonsForCancellation();
            $title = $this->sub_reason
                ? getCancellationSubReasonTitle($details, $this->sub_reason)
                : null;
            return $title ? $title['en'] : null;
        });

        $grid->column('future_plan', __('Future Plan'))->display(function () {
            $details = getReasonsForCancellation();
            $title = $this->future_plan
                ? getCancellationFuturePlanReasonTitle(
                    $details,
                    $this->future_plan
                )
                : null;
            return $title ? $title['en'] : null;
        });
        $grid->column('comment', __('Comment'))->limit(30);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->quickSearch(function ($model, $query) {
            return $model->whereHas('user', function ($subquery) use ($query) {
                $subquery->where(function ($subquery) use ($query) {
                    // Rename the parameter here
                    $subquery
                        ->whereHas('jobseeker', function ($subquery) use (
                            $query
                        ) {
                            $subquery->whereRaw(
                                "CONCAT(first_name, ' ', last_name) LIKE ?",
                                ["%$query%"]
                            );
                        })
                        ->orWhereHas('company', function ($subquery) use (
                            $query
                        ) {
                            $subquery->where(
                                'company_name',
                                'like',
                                "%$query%"
                            );
                        });
                });
            });
        });

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
        $show = new Show(ReasonForCancellation::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReasonForCancellation());

        return $form;
    }
}
