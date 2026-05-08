<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'status'             => $this->status,
            'email_verified_at'  => $this->email_verified_at,
            'roles'              => $this->getRoleNames(),
            'profile'            => $this->whenLoaded('profile', fn () => [
                'first_name'   => $this->profile->first_name,
                'last_name'    => $this->profile->last_name,
                'full_name'    => $this->full_name,
                'avatar_url'   => $this->profile->avatar_url,
                'locale'       => $this->profile->locale,
                'timezone'     => $this->profile->timezone,
                'date_of_birth'=> $this->profile->date_of_birth?->format('Y-m-d'),
            ]),
            'created_at'         => $this->created_at->toIso8601String(),
        ];
    }
}
