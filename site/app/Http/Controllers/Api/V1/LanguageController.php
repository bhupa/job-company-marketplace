<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Language\LanguageStoreRequest;
use App\Http\Resources\V1\User\UserDetailsResources;

class LanguageController extends BaseController
{
    public function store(LanguageStoreRequest $request)
    {
        setUserLanguage($request->language);

        return $this->success(
            new UserDetailsResources(auth()->user()),
            'User language set successfully'
        );
    }
}
