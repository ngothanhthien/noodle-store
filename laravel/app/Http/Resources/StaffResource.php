<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'rules' => RuleResource::collection($this->whenLoaded('rules')),
            'orders_today'=>$this->whenNotNull($this->orders_today),
            'orders_this_month'=>$this->whenNotNull($this->orders_this_month),
        ];
    }
}
