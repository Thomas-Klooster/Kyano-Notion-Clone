<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        'visibility' => $this->visibility,
        'slug' => $this->slug,
        'created_at' => $this->created_at->locale("nl")->diffForHumans(),
        'updated_at' => $this->updated_at->locale("nl")->diffForHumans(),
        'tags' => $this->whenLoaded('tags', fn() => $this->tags->pluck('name')),
        'project' => new ProjectResource($this->whenLoaded('project')),
        'attachments' => $this->whenLoaded('attachments', fn() => $this->attachments->map(fn($attachment) => [
            'id' => $attachment->id,
            'path' => $attachment->path,
            'url' => Storage::url($attachment->path),
            'mime' => $attachment->mime,
            'original_name' => $attachment->original_name,
            'name' => $attachment->original_name,
            'size' => $attachment->size,
        ])->values()),

    ];
    }
}
