<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class PostApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray($request): array
    // {
    //     // Normalize tags to array even if stored as CSV
    //     $tags = $this->tags;
    //     if (is_string($tags)) {
    //         $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));
    //     }

    //     return [
    //         'id'                 => $this->id,
    //         'title'              => $this->title,
    //         'slug'               => $this->slug,
    //         'content'            => $this->content,             // HTML (from RichEditor)
    //         'short_description'  => $this->short_description,

    //         // Author & Category (included only if loaded)
    //         'author'   => $this->whenLoaded('author', function () {
    //             return [
    //                 'id'   => $this->author->id,
    //                 'name' => $this->author->name,
    //                 'email'=> $this->author->email ?? null,
    //             ];
    //         }),
    //         'category' => $this->whenLoaded('category', function () {
    //             return [
    //                 'id'   => $this->category->id,
    //                 'name' => $this->category->name,
    //                 'slug' => $this->category->slug ?? null,
    //             ];
    //         }),

    //         'published_date'     => optional($this->published_date)->toISOString(),
    //         'tags'               => $tags,

    //         // Media (public URLs)
    //         'image_path'         => $this->image,
    //         'image_url'          => $this->image ? Storage::disk('public')->url($this->image) : null,
    //         'meta_image_path'    => $this->meta_image,
    //         'meta_image_url'     => $this->meta_image ? Storage::disk('public')->url($this->meta_image) : null,

    //         // SEO
    //         'meta_title'         => $this->meta_title,
    //         'meta_description'   => $this->meta_description,
    //         'meta_keywords'      => $this->meta_keywords,

    //         'created_at'         => optional($this->created_at)->toISOString(),
    //         'updated_at'         => optional($this->updated_at)->toISOString(),
    //     ];
    // }
    public function toArray($request): array
    {
        // Normalize tags (CSV -> array)
        $tags = $this->tags;
        if (is_string($tags)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,

            // Rich HTML content (from Filament RichEditor)
            'content'           => $this->content,
            'short_description' => $this->short_description,

            // Relations (only when eager loaded)
            'author' => $this->whenLoaded('author', fn () => [
                // 'id'    => $this->author->id,
                'name'  => $this->author->name,
                // 'email' => $this->author->email ?? null,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                // 'id'   => $this->category->id,
                'name' => $this->category->name,
                // 'slug' => $this->category->slug ?? null,
            ]),

            // Dates (ISO 8601)
            'published_date'    => optional($this->published_date)->toISOString(),
            // 'created_at'        => optional($this->created_at)->toISOString(),
            // 'updated_at'        => optional($this->updated_at)->toISOString(),

            // Tags
            'tags'              => $tags,

            // Media (public URLs if stored)
            // 'image_path'        => $this->image,
            'image_url'         => $this->image ? Storage::disk('public')->url($this->image) : null,
            // 'meta_image_path'   => $this->meta_image,
            'meta_image_url'    => $this->meta_image ? Storage::disk('public')->url($this->meta_image) : null,

            // SEO
            'meta_title'        => $this->meta_title,
            'meta_description'  => $this->meta_description,
            'meta_keywords'     => $this->meta_keywords,
        ];
    }
}
