<?php

namespace App\Http\Resources\V1\Matching\Pagination;

use App\Http\Resources\V1\Matching\MatchingResource;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MatchingPaginationResource extends ResourceCollection
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
            'data' => MatchingResource::collection($this->collection),
            'pagination' => $this->pagination,
        ];
    }
}
