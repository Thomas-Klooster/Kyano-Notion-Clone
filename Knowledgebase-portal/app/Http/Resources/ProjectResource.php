<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ArticleResource;
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
            'slug' => $this->slug,
            'created_at' => $this->created_at->locale("nl")->diffForHumans(),
            'updated_at' => $this->updated_at->locale("nl")->diffForHumans(),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'workspace' => $this->workspace->name,
            'workspace_slug' => $this->workspace->slug,
        ];
    }
}
