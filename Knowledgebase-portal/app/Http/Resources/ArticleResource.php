<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
        'title' => $this->title,
        'content' => $this->content, 
        'summary' => $this->summary,
        'status' => $this->status,
        'created_at' => $this->created_at->locale("nl")->diffForHumans(),
        'updated_at' => $this->updated_at->locale("nl")->diffForHumans(),

    ];
    }
}
