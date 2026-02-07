<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'coupon_id' => $this->coupon_id,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'notes' => $this->notes,
            'confirmed_at' => $this->confirmed_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'items' => OrderProductResource::collection($this->whenLoaded('orderProducts')),
            'rating' => new RatingResource($this->whenLoaded('rating')),
        ];
    }
}
