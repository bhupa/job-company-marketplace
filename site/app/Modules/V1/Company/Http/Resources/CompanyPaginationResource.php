<?php

namespace App\Modules\V1\Company\Http\Resources;

use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyPaginationResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
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
            'data' => CompanyDetailsResource::collection($this->collection),
            'pagination' => $this->pagination,
        ];
        }
}