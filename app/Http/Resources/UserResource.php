<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name'=>$this->first_name,
            'last_name'=> $this->last_name,
            'email'=>$this->email,
            'address'=>$this->address,
            'stripe_id'=> $this->stripe_id,
            'trial_ends_at'=> $this->stripe_id,
            'mobile_num'=> $this->mobile_num,
            'has_moodle'=> $this->has_moodle,
            'has_socialite'=> $this->has_socialite,
            'profile_image'=> $this->profile_image,
            'last_updated'=> $this->updated_at,
            'roles'=>$this->roles
        ];
    }
}
