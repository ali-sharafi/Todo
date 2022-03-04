<?php

namespace Todo\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LabelCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => LabelResource::collection($this->collection),
        ];
    }
}
