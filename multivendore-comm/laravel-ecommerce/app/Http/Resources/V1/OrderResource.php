<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'order_number'    => $this->order_number,
            'status'          => $this->status,
            'currency'        => $this->currency,
            'subtotal'        => $this->subtotal,
            'tax_amount'      => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount'    => $this->total_amount,
            'notes'           => $this->notes,
            'placed_at'       => $this->placed_at->toIso8601String(),
            'items'           => $this->whenLoaded('items', fn () =>
                $this->items->map(fn ($item) => [
                    'id'                 => $item->id,
                    'sku'                => $item->variant?->sku,
                    'quantity'           => $item->quantity,
                    'unit_price'         => $item->unit_price,
                    'subtotal'           => $item->subtotal,
                    'fulfillment_status' => $item->fulfillment_status,
                    'shipment'           => $this->when($item->relationLoaded('shipment'), fn () => [
                        'carrier'         => $item->shipment?->carrier,
                        'tracking_number' => $item->shipment?->tracking_number,
                        'status'          => $item->shipment?->status,
                        'delivered_at'    => $item->shipment?->delivered_at?->toIso8601String(),
                    ]),
                ])
            ),
            'payment'         => $this->whenLoaded('payment', fn () => [
                'method'   => $this->payment?->method,
                'status'   => $this->payment?->status,
                'amount'   => $this->payment?->amount,
                'currency' => $this->payment?->currency,
            ]),
            'address'         => $this->whenLoaded('address', fn () => [
                'full_name'   => $this->address?->full_name,
                'formatted'   => $this->address?->formatted,
                'country_code'=> $this->address?->country_code,
            ]),
        ];
    }
}
