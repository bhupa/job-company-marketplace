<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Content\ContentDetailsJaResource;
use App\Http\Resources\V1\Content\ContentDetailsResource;
use App\Models\V1\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ContentController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->has('type')) {
            $result =
                $request->type == 'terms_of_service_company'
                    ? 'terms_of_service'
                    : $request->type;
            $dataEn = Content::where('type', $result)
                ->select('title_en', 'type', 'link', 'description_en')
                ->first();
            $dataJa = Content::where('type', $result)
                ->select('title_ja', 'type', 'link', 'description_ja')
                ->first();
            $data = [
                'en' => $dataEn ? new ContentDetailsResource($dataEn) : null,
                'ja' => $dataJa ? new ContentDetailsJaResource($dataJa) : null,
            ];

            return $this->success($data, 'Content lists');
        }

        return $this->errors(['message' => 'Not found!'], 400);
    }

    public function termsofservice(Request $request)
    {
        if ($request->user_type == 1) {
            $data['label_en'] = Lang::get('admin.terms_of_service', [], 'en');
            $data['label_ja'] = Lang::get('admin.terms_of_service', [], 'ja');
        } else {
            $data['label_en'] = Lang::get(
                'admin.terms_of_service_company',
                [],
                'en'
            );
            $data['label_ja'] = Lang::get(
                'admin.terms_of_service_company',
                [],
                'ja'
            );
        }

        return $this->success($data, 'Terms of service details');
    }

    public function privacyPolicy(Request $request)
    {
        $data['label_en'] = Lang::get('admin.privacy_policy', [], 'en');
        $data['label_ja'] = Lang::get('admin.privacy_policy', [], 'ja');

        return $this->success($data, 'Privacy Policy details');
    }

    public function userPolicy(Request $request)
    {
        $data['label_en'] = Lang::get('admin.user_policy', [], 'en');
        $data['label_ja'] = Lang::get('admin.user_policy', [], 'ja');

        return $this->success($data, 'User Policy details');
    }
}
