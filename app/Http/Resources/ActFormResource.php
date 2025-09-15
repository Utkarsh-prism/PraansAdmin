<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ActFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'form_no'    => $this->form_no,
            'title'      => $this->title,
            'short_desc' => $this->short_desc,
            'pdf_url'    => $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
