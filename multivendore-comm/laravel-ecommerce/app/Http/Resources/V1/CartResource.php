<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'currency'   => $this->currency,
            'item_count' => $this->item_count,
            'total'      => $this->total,
            'items'      => $this->whenLoaded('items', fn () =>
                $this->items->map(fn ($item) => [
                    'id'         => $item->id,
                    'variant_id' => $item->variant_id,
                    'sku'        => $item->variant?->sku,
                    'name'       => $item->variant?->product?->getTranslation(app()->getLocale())?->name,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal'   => round($item->unit_price * $item->quantity, 2),
                ])
            ),
            'expires_at' => $this->expires_at->toIso8601String(),
        ];
    }
}
