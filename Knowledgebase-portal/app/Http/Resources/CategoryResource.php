<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProjectResource;

class CategoryResource extends JsonResource
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
        'slug' => $this->slug,
        'created_at' => $this->created_at->locale("nl")->diffForHumans(),
        'updated_at' => $this->updated_at->locale("nl")->diffForHumans(),
         'projects' => ProjectResource::collection($this->whenLoaded('projects')),
         'workspace' => $this->workspace->name,
    ];
}
}
