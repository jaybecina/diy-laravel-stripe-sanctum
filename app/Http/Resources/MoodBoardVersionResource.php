<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MoodBoardVersionItemResource;

class MoodBoardVersionResource extends JsonResource
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
            'mood_board_id'=>$this->mood_board_id,
            'version'=> $this->version,
            'remarks'=>$this->remarks,
            'status'=>$this->status,
            'version_items'=> MoodBoardVersionItemResource::collection($this->version_items)
        ];
    }
}
