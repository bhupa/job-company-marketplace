<?php

namespace App\Admin\Actions;

use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Actions\RowAction;
use Illuminate\Support\Facades\App;

class CompanyDelete extends RowAction
{
    public $name = 'delete';

    public $icon = 'icon-trash';

   

    public function handle(Model $model)
    {
        // $model ...
        $company  = Company::find($this->getKey());
        $user = User::find($company->user_id);
        $name = $user->fullName;

        $userService = App::make(UserService::class);
        // Use the resolved userService instance to delete the user account
        $userService->deleteUserAccount($user);
        

        return $this->response()->success($name.'company account has been deleted successfullly.')->refresh();
    }

}