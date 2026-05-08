<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'sku'            => $this->sku,
            'price'          => $this->price,
            'sale_price'     => $this->sale_price,
            'effective_price'=> $this->effective_price,
            'currency'       => $this->currency,
            'stock_quantity' => $this->stock_quantity,
            'in_stock'       => $this->isInStock(),
            'is_active'      => $this->is_active,
            'weight_grams'   => $this->weight_grams,
            'attributes'     => $this->whenLoaded('attributeValues', fn () =>
                $this->attributeValues->map(fn ($av) => [
                    'attribute'       => $av->attribute?->name,
                    'value'           => $av->value,
                ])
            ),
        ];
    }
}
