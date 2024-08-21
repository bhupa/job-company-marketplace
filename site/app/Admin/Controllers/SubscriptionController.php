<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\AddSubscription;
use App\Events\V1\SuperChatEvent;
use App\Models\ChatRoom;
use App\Models\User;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\V1\InAppPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\FlareClient\FlareMiddleware\AddSolutions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Subscribed Users';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $grid
            ->model()
            ->whereNotNull('subscriptions_type')
            ->where('subscriptions_type' ,'!=', 'trial');
      
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'))->display(function () {
           return $this->fullName;
        });
         $grid->column('subscriptions_type', __('Payment Type'));
        // $grid->column('item_id', __('Item id'));
        $grid->column('ends_at', __('End Date'))->display(function () {

            return $this->subscribedType ? $this->subscribedType->ends_at :'';
            })->dateFormat('Y-m-d');
        $grid->column('status', __('Status'))->display(function () {
            $status = '';
            if($this->subscriptions_type == 'iap' || $this->subscriptions_type  == 'esewa' || $this->subscriptions_type == 'admin-pay'){
                Log::info('stripe admin dashboard issues');
                Log::info($this->subscribedType);
                 $status = $this->subscribedType ? $this->subscribedType->status : 'null';
            }elseif($this->subscriptions_type == 'stripe'){
                $status = $this->subscribedType->stripe_status;
            }
            return $status;
        });
        $grid->column('created_at', __('Created at'))->display(function () {

            return $this->subscribedType ? $this->subscribedType->created_at : '';
            })->dateFormat('Y-m-d');
        $grid->column('updated_at', __('Updated at'))->display(function () {

            return $this->subscribedType ? $this->subscribedType->updated_at : '';
            })->dateFormat('Y-m-d');
       
        $grid->actions(function ($actions) {
            $actions->add(new AddSubscription());
            $actions->disableEdit();
        });

        $grid->quickSearch(function ($model, $query) {
            return $model->whereHas('jobseeker', function ($subquery) use (
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

        $form
            ->select('user_id', __('User'))
            ->options(User::all()->pluck('fullName', 'id'))
            ->load('item_id', '/api/v1/getPlan');
        $form->select('item_id', __('Item'))->options([]);
        $form->hidden('status')->value('active');
        $form->saved(function (Form $form) {
            // Get the in-app purchase model instance
            $output = $form->model();
            // Get the user instance related to the in-app purchase

            $currentDate = Carbon::now();
            $ends_at = null;
            $paymentFor = null;

            if (Str::contains($form->input('item_id'), 'one_week')) {
                $ends_at = $currentDate->addWeeks(1);
                $paymentFor = 'subscription';
            } elseif (Str::contains($form->input('item_id'), 'one_month')) {
                $ends_at = $currentDate->addMonths(1);
                $paymentFor = 'subscription';
            } elseif (Str::contains($form->input('item_id'), 'three_months')) {
                $ends_at = $currentDate->addMonths(3);
                $paymentFor = 'subscription';
            } elseif (Str::contains($form->input('item_id'), 'six_months')) {
                $ends_at = $currentDate->addMonths(6);
                $paymentFor = 'subscription';
            } elseif (Str::contains($form->input('item_id'), 'super_chat')) {
                $ends_at = null;
                $paymentFor = 'super_chat';
            }

            $subscriptionType = 'admin-pay';
            $data = [
                'payment_type' => 'admin-pay',
                'payment_for' => $paymentFor,
                'ends_at' => $ends_at ? $ends_at->toDateTimeString() : null,
            ];
       
            // Update user's subscription type

            tap($output->update($data));

            $output->user->update(['subscriptions_type' => $subscriptionType]);
            // Handle chat room logic if needed
            // Replace $request with the appropriate request variable name if needed
            if ($form->input('chat_room_id')) {
                $room = ChatRoom::find($form->input('chat_room_id'));
                if ($room) {
                    $room->update([
                        'payment_type' => 'iap',
                        'in_app_id' => $output->id,
                    ]);
                    $receiveBy = chatroomReceiveBy($room);
                    if ($receiveBy) {
                        $event = [
                            'receiveBy' => $receiveBy->id,
                            'chatRoomId' => (string) $room->id,
                        ];
                        broadcast(new SuperChatEvent($event));
                    }
                }
            }
        });

        return $form;
    }
}
