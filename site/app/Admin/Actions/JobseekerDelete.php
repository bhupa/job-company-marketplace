<?php

namespace App\Admin\Actions;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Actions\RowAction;
use Illuminate\Support\Facades\App;
use App\Models\Jobseeker;
use App\Models\User;
use App\Services\UserService;

class JobseekerDelete extends RowAction
{
    public $name = 'delete';

    public $icon = 'icon-trash';

    public function handle(Model $model)
        {
        // $model ...
        $company = Jobseeker::find($this->getKey());
        $user = User::find($company->user_id);
        $name = $user->fullName;

        $userService = App::make(UserService::class);
        // Use the resolved userService instance to delete the user account
        $userService->deleteUserAccount($user);

        return $this->response()->success($name . 'jobseeker account has been deleted successfullly.')->refresh();
        }

}