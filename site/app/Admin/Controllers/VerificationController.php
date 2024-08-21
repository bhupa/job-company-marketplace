<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\User;
use App\Services\FireBaseService;

class VerificationController extends AdminController
{
    protected $fireBaseService;
    public function __construct(FireBaseService $fireBaseService)
    {
        $this->fireBaseService = $fireBaseService;
    }
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User Verification';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));

        $grid->column('name', __('Name'))->display(function () {
            // Assuming you have a 'type' column in the User model
            if ($this->user_type == 1) {
                return $this->jobseeker->first_name .
                    ' ' .
                    $this->jobseeker->last_name;
            } elseif ($this->user_type == 2) {
                return $this->company->company_name;
            }
        });
        $grid
            ->column('user_type', __('User type'))
            ->using(['1' => 'Jobseeker', '2' => 'Company']);
        $grid->column('email', __('Email'));
        $grid->column('country_code', __('Country code'));
        $grid->column('phone', __('Phone'));
        $states = [
            'on' => ['value' => 1, 'text' => 'open', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'close', 'color' => 'primary'],
        ];
        $grid
            ->column('is_verify', __('Is Verified'))
            ->display(function ($value) {
                // Assuming you have a 'user_type' column in the User model
                if ($this->user_type == 1) {
                    return $this->jobseeker->is_verify;
                } elseif ($this->user_type == 2) {
                    return $this->company->is_verify;
                }
            })
            ->switch($states);
        $grid->column('comment', 'Comment');
        $grid->actions(function ($actions) {
            // $actions->disableEdit();
        });
        $grid->disableCreateButton();
        $grid->model()->where(function ($query) {
            $query
                ->where(function ($subquery) {
                    $subquery
                        ->where('user_type', 1)
                        ->whereHas('documents')
                        ->whereHas('jobseeker', function ($query) {
                            $query->where('is_verify', null);
                        });
                })
                ->orWhere(function ($subquery) {
                    $subquery
                        ->where('user_type', 2)
                        ->whereHas('documents')
                        ->whereHas('company', function ($query) {
                            $query->where('is_verify', null);
                        });
                });
        });
        $grid->quickSearch(function ($model, $query) {
            return $model->where(function ($subquery) use ($query) {
                // Rename the parameter here
                $subquery
                    ->whereHas('jobseeker', function ($subquery) use ($query) {
                        $subquery->whereRaw(
                            "CONCAT(first_name, ' ', last_name) LIKE ?",
                            ["%$query%"]
                        );
                    })
                    ->orWhereHas('company', function ($subquery) use ($query) {
                        $subquery->where('company_name', 'like', "%$query%");
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
        $show = new Show(User::findOrFail($id));
        $show->field('full_name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('country_code', __('Country code'));
        $show->field('phone', __('Phone'));
        $show
            ->field('user_type', __('User type'))
            ->using(['1' => 'Jobseeker', '2' => 'Company']);
        $show->field('status', __('Status'))->using([
            '0' => 'pending',
            '1' => 'active',
            '2' => 'deactivated',
            '3' => 'blocked',
        ]);
        $show->field('comment', __('Comment'));
        $show->documents('Document', function ($author) {
            $author->setResource('/admin/image-files');
            $author
                ->image()
                ->image(url('/') . '/storage', 100, 100)
                ->link(function ($file) {
                    return url('') . '/storage/' . $file->image;
                });

            $author->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableShow();
            });
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
        $form = new Form(new User());
        $form->textarea('comment', __('Comment'));

        $form->saving(function (Form $form) {
            // Get the user model instance
            $user = $form->model();

            if ($form->input('is_verify')) {
                // Check user type
                if ($user->user_type == 1) {
                    // If user type is 1 (jobseeker), update jobseeker's verification status
                    $user->jobseeker->update(['is_verify' => 1]);
                } elseif ($user->user_type == 2) {
                    // If user type is 2 (company), update company's verification status
                    $user->company->update(['is_verify' => 1]);
                }
                // Update comment value if it exists
                tap($user->update(['comment' => null]));
            }
            if ($user->device_token) {
                if (empty($user->comment)) {
                    $form->input('is_verify')
                        ? $this->fireBaseService->sendOtp(
                            $user,
                            'user-account-verify',
                            $user
                        )
                        : null;
                } else {
                    $this->fireBaseService->sendOtp(
                        $user,
                        'user-account-rejected',
                        $user
                    );
                }
                totalbadgeCount($user);
            }
        });

        return $form;
    }
}
