<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at->locale("ja")->diffForHumans(),
            'updated_at' => $this->updated_at->locale("ja")->diffForHumans(),
            'articles' => $this->whenLoaded('articles'),
        ];
    }
}
