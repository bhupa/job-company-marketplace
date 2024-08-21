<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\InAppPurchase;

class IAPController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'InAppPurchase';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new InAppPurchase());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('item_id', __('Item id'));
        $grid->column('status', __('Status'));
        $grid->column('purchase_token', __('Purchase token'));
        $grid->column('payment_type', __('Payment type'));
        $grid->column('payment_for', __('Payment for'));
        $grid->column('transaction_receipt', __('Transaction receipt'));
        $grid->column('trial_ends_at', __('Trial ends at'));
        $grid->column('ends_at', __('Ends at'));
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
        $show = new Show(InAppPurchase::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('item_id', __('Item id'));
        $show->field('status', __('Status'));
        $show->field('purchase_token', __('Purchase token'));
        $show->field('payment_type', __('Payment type'));
        $show->field('payment_for', __('Payment for'));
        $show->field('transaction_receipt', __('Transaction receipt'));
        $show->field('trial_ends_at', __('Trial ends at'));
        $show->field('ends_at', __('Ends at'));
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
        $form = new Form(new InAppPurchase());

        $form->number('user_id', __('User id'));
        $form->text('item_id', __('Item id'));
        $form->text('status', __('Status'));
        $form->textarea('purchase_token', __('Purchase token'));
        $form->text('payment_type', __('Payment type'));
        $form->text('payment_for', __('Payment for'));
        $form->textarea('transaction_receipt', __('Transaction receipt'));
        $form
            ->datetime('trial_ends_at', __('Trial ends at'))
            ->default(date('Y-m-d H:i:s'));
        $form->datetime('ends_at', __('Ends at'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
