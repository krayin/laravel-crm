<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'subject'           => $this->subject,
            'description'       => $this->description,
            'billing_address'   => $this->billing_address,
            'shipping_address'  => $this->shipping_address,
            'discount_percent'  => $this->discount_percent,
            'discount_amount'   => $this->discount_amount,
            'tax_amount'        => $this->tax_amount,
            'adjustment_amount' => $this->adjustment_amount,
            'sub_total'         => $this->sub_total,
            'grand_total'       => $this->grand_total,
            'expired_at'        => $this->expired_at,
            'user'              => new UserResource($this->user),
            'person'            => new PersonResource($this->person),
            'leads'             => LeadResource::collection($this->leads),
        ];
    }
}
