<?php

namespace Todo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Todo\Http\Resources\LabelResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'labels' => LabelResource::collection($this->labels)
        ];
    }
}
