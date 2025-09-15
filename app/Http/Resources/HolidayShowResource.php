<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class HolidayShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'              => (int) $this->id,
            'state'           => (string) $this->state,
            'title'           => (string) $this->title,
            'slug'            => (string) $this->slug,
            'short_desc'      => $this->short_desc, // HTML allowed (sanitize on frontend)
            'holiday_pdf_url' => $this->holiday_pdf_url, // accessor from model

            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($d) {
                    return [
                        'id'            => (int) $d->id,
                        'holiday_name'  => (string) $d->holiday_name,
                        'type'          => (string) $d->type,        // Regional/National/Optional
                        'date'          => $d->date,                 // Y-m-d
                        'date_formatted'=> $d->date ? Carbon::parse($d->date)->format('d-m-Y') : null,
                        'day'           => (string) $d->day,
                        'month'         => (string) $d->month,
                        'sort_order'    => $d->sort_order !== null ? (int) $d->sort_order : null,
                    ];
                });
            }),
        ];
    }
}
