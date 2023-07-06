<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EventResource;

class UserEventResource extends JsonResource
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
            'event_id'=>$this->event_id,
            'event'=> new EventResource($this->event),
            'user_id'=>$this->user_id,
            'is_booked'=> $this->is_booked,
            'amount_paid'=>$this->amount_paid
        ];
    }
}
