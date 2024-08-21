<?php

namespace App\Http\Resources\V1\Jobseeker;

use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobseekerPaginationResource extends ResourceCollection
{
    use PaginationTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
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
