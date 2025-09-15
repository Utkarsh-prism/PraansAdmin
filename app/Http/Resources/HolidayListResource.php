<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'state' => (string) $this->state,
            'slug'  => (string) $this->slug,
            // Agar title bhi bhejna ho to uncomment:
            // 'title' => (string) $this->title,
        ];
    }
}
