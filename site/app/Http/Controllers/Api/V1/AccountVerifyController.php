<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Auth\VerifyAccountStoreRequest;
use App\Http\Resources\V1\User\UserDetailsResources;
use App\Notifications\V1\Account\AccountNotification;
use App\Services\ImageFileService;
use App\Services\UserService;

class AccountVerifyController extends BaseController
{
    protected $userService;
    protected $image;

    public function __construct(
        UserService $userService,
        ImageFileService $image
    ) {
        $this->userService = $userService;
        $this->image = $image;
    }

    public function store(VerifyAccountStoreRequest $request)
    {
        try {
            if ($request->hasFile('images')) {
                $this->userService->verifyUserAccount($request);
                auth()
                    ->user()
                    ->update(['comment' => null]);
            }
            if ($request->has('remove')) {
                $this->image->deleteBulkImage($request->remove);
            }
            $data = new UserDetailsResources(auth()->user());

            return $this->success(
                $data,
                trans(
                    'lang.user.account.document_submit',
                    [],
                    getUserLanguage(auth()->user())
                )
            );
        } catch (\Throwable $th) {
            return $this->errors(['message' => $th->getMessage()], 400);
        }
    }

    public function getVerifyDetails()
    {
        try {
            $data = new UserDetailsResources(auth()->user());
            auth()
                ->user()
                ->unreadNotifications()
                ->where('type', AccountNotification::class)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            totalbadgeCount(auth()->user());

            return $this->success($data, 'Verification details');
        } catch (\Throwable $th) {
            return $this->errors(['message' => $th->getMessage()], 400);
        }
    }
}
