<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'customer' => $this->whenLoaded('customer'),
            'user' => $this->whenLoaded('user'),
            'state' =>$this->state,
            'total_price' =>$this->total_price,
            'payment_gate' => $this->payment_gate,
            'created_at' => date('d-m-Y h:ia', strtotime($this->created_at)),
            'meals' => MealPreviewResource::collection($this->whenLoaded('meals')),
        ];
    }
}
