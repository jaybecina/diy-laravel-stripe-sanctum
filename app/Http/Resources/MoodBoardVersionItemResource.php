<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MoodBoardItemResource;

class MoodBoardVersionItemResource extends JsonResource
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
            'mood_board_version_id'=>$this->mood_board_version_id,
            'mood_board_item_id'=> $this->mood_board_item_id,
            "x"=>$this->x,
            "y"=>$this->y,
            "h"=>$this->h,
            "w"=>$this->w,
            'index'=>$this->index,
            'remarks'=>$this->remarks,
            'item'=> new MoodBoardItemResource($this->item)
        ];
    }
}
