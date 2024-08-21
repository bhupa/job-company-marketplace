<?php

namespace App\Modules\V1\Jobseeker\Http\Resources;

use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobseekerPaginationResource extends ResourceCollection
{
    use PaginationTrait;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    private $pagination;

    public function __construct($resource)
        {
        $this->pagination = $this->paginate($resource);
        $resource = $resource->getCollection();
        parent::__construct($resource);
        }

    public function toArray(Request $request): array
        {
        return [
            'data' => JobseekerDetailsResource::collection($this->collection),
            'pagination' => $this->pagination,
        ];
        }
}