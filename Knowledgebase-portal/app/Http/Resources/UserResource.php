<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // 'address' => $this->address,
            // 'company' => $this->company,
            // 'phone_number' => $this->phone,
            'role' => $this->role,
        ];
    }
}