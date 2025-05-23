<?php

namespace Webkul\RestApi\Http\Resources\V1\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'quantity'    => $this->quantity,
            'price'       => $this->price,
            'amount'      => $this->amount,
            'product'     => new ProductResource($this->product),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
