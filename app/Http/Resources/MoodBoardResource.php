<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MoodBoardVersionResource;

class MoodBoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'user_id'=> $this->user_id,
            'frame_color'=>$this->frame_color,
            'frame_background'=>$this->frame_background,
            'inspiration_picture'=>$this->inspiration_picture,
            'image_placeholder'=>$this->image_placeholder,
            'versions'=> MoodBoardVersionResource::collection($this->versions)
        ];
    }
}
