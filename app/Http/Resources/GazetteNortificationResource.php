<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\State;

class GazetteNortificationResource extends JsonResource
{
    public function toArray($request)
    {
        // map state slug -> name once (cached static)
        static $stateMap = null;
        if ($stateMap === null) {
            $stateMap = State::query()->pluck('name', 'slug')->toArray();
        }

        $pdfUrl = $this->pdf_path ? url(Storage::url($this->pdf_path)) : null;

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'short_description' => $this->short_description,
            'description'       => (string) $this->description,   // HTML allowed

            'state_slug'        => $this->state,
            'state_name'        => $stateMap[$this->state] ?? $this->state,

            'updated_date'      => optional($this->updated_date)->format('Y-m-d'),
            'effective_date'    => optional($this->effective_date)->format('Y-m-d'),

            'pdf_path'          => $this->pdf_path,
            'pdf_url'           => $pdfUrl,

            'created_at'        => optional($this->created_at)?->toIso8601String(),
            'updated_at'        => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
