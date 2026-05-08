<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale();
        $trans  = $this->getTranslation($locale);

        return [
            'id'           => $this->id,
            'asin'         => $this->asin,
            'status'       => $this->status,
            'avg_rating'   => $this->avg_rating,
            'total_reviews'=> $this->total_reviews,
            'name'         => $trans?->name,
            'description'  => $trans?->description,
            'short_description' => $trans?->short_description,
            'meta_title'   => $trans?->meta_title,
            'translations' => $this->whenLoaded('translations'),
            'variants'     => VariantResource::collection($this->whenLoaded('variants')),
            'media'        => $this->whenLoaded('media', fn () =>
                $this->media->map(fn ($m) => [
                    'url'       => $m->url,
                    'type'      => $m->type,
                    'alt_text'  => $m->alt_text,
                    'sort_order'=> $m->sort_order,
                ])
            ),
            'brand'        => $this->whenLoaded('brand', fn () => [
                'id'   => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'category'     => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'slug' => $this->category->slug,
                'name' => $this->category->getTranslation($locale)?->name,
            ]),
            'vendor'       => $this->whenLoaded('vendor', fn () => [
                'id'         => $this->vendor->id,
                'store_name' => $this->vendor->store_name,
                'slug'       => $this->vendor->slug,
                'avg_rating' => $this->vendor->profile?->avg_rating,
            ]),
            'lowest_price' => $this->getLowestPrice(),
            'created_at'   => $this->created_at->toIso8601String(),
        ];
    }
}
