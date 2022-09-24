<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'customer'=>$this->whenLoaded('customer'),
            'total_price' => $this->total_price,
            'payment_gate' => $this->payment_gate,
            'state' => $this->state,
            'created_at' => date('d-m-Y h:ia', strtotime($this->created_at)),
        ];
    }
}
