<?php

namespace App\Services\V1;

use App\Models\V1\Tag;
use App\Services\BaseService;

class TagService extends BaseService
{
    protected $tag;

    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    public function fetch($request)
    {
        $type = $request->type == 'jobseeker' ? 'jobseeker' : 'job';

        return $this->model->where('type', $type)->get();
    }

    public function create($models, $tags)
    {
        $models->tags()->sync($tags);

        return $models;
    }
}
