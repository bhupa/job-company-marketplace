<?php

namespace App\Traits;

trait PaginationTrait
{
    public function paginate($resource)
    {
        return [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'perPage' => $resource->perPage(),
            'currentPage' => $resource->currentPage(),
            'lastPage' => $resource->lastPage(),
            'from' => $resource->firstItem(),
            'to' => $resource->lastItem(),
            'firstPageUrl' => $resource->url(1),
            'nextPageUrl' => $resource->nextPageUrl(),
            'prevPageUrl' => $resource->previousPageUrl(),
            'lastPageUrl' => $resource->url($resource->lastPage()),
        ];
    }
}
