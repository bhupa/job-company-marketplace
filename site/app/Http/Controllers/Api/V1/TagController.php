<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Tag\TagDetailsResource;
use App\Services\V1\TagService;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    protected $tag;

    public function __construct(TagService $tag)
    {
        $this->tag = $tag;
    }

    public function index(Request $request)
    {
        if ($request->has('type')) {
            $tags = $this->tag->fetch($request);

            return $this->success(
                TagDetailsResource::collection($tags),
                'Tag lists'
            );
        }

        return $this->errors([], 400);
    }
}
