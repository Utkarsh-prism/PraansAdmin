<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ActRuleFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'state'             => $this->state,
            'short_description' => $this->short_description,
            'act_desc'          => $this->act_desc,   // HTML string as-is
            'rule_desc'         => $this->rule_desc,  // HTML string as-is

            'act_doc_path'      => $this->upload_path,
            'act_doc_url'       => $this->upload_path ? Storage::disk('public')->url($this->upload_path) : null,
            'rule_doc_path'     => $this->form_image_path,
            'rule_doc_url'      => $this->form_image_path ? Storage::disk('public')->url($this->form_image_path) : null,
            'section_count'     => (int) $this->section_count,
            'rule_count'        => (int) $this->rule_count,
            'form_count'        => (int) $this->form_count,

            // 'forms_count'       => $this->when(isset($this->forms_count), $this->forms_count),
            'forms'             => ActFormResource::collection($this->whenLoaded('forms')),

            'created_at'        => optional($this->created_at)->toISOString(),
        ];
    }
}
