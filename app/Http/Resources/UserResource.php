<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone'=> $this->profile->phone,
            'address'=> $this->profile->address,
            'role'=> ucfirst($this->role),
            'image' => $this->profile->avatar_url,
            'status' => $this->email ? 'Active' : 'Inactive'
        ];
    }
}
