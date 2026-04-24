<?php

namespace App\Traits;

use App\Models\Tag;

trait HasTags
{
    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function attachTags(array $tagNames) {
        $ids = collect($tagNames)->map(
            fn($name) => Tag::firstOrFail(['name' => $name])->id
        );
        $this->tags()->syncWithoutDetaching($ids);
    }

    public function syncTags(array $tagNames) {
        $ids = collect($tagNames)->map(
            fn($name) => Tag::firstOrCreate(['name' => $name])->id
        );
        $this->tags()->sync($ids);
    }

}