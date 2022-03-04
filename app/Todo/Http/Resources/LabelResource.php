<?php

namespace Todo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LabelResource extends JsonResource
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
            'label' => $this->name,
            'tasks_count' => $this->tasks()->where('user_id', auth()->user()->id)->count()
        ];
    }
}
