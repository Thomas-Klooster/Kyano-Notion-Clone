<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
    'user' => new UserResource($this->whenLoaded('user')),
    'article' => new ArticleResource($this->whenLoaded('article')),
    'helpful' => (bool) $this->helpful,
    'feedback' => $this->feedback,
    'created_at' => $this->created_at->locale("nl")->diffForHumans(),
    'updated_at' => $this->updated_at->locale("nl")->diffForHumans(),
    
    ];
    }
}
