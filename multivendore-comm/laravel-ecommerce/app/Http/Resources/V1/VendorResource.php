<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'store_name'      => $this->store_name,
            'slug'            => $this->slug,
            'status'          => $this->status,
            'plan_type'       => $this->plan_type,
            'commission_rate' => $this->commission_rate,
            'approved_at'     => $this->approved_at?->toIso8601String(),
            'profile'         => $this->whenLoaded('profile', fn () => [
                'description'   => $this->profile?->description,
                'logo_url'      => $this->profile?->logo_url,
                'banner_url'    => $this->profile?->banner_url,
                'business_type' => $this->profile?->business_type,
                'avg_rating'    => $this->profile?->avg_rating,
                'total_reviews' => $this->profile?->total_reviews,
                'website_url'   => $this->profile?->website_url,
            ]),
            'owner'           => $this->whenLoaded('owner', fn () => [
                'id'    => $this->owner?->id,
                'email' => $this->owner?->email,
                'name'  => $this->owner?->full_name,
            ]),
            'created_at'      => $this->created_at->toIso8601String(),
        ];
    }
}
