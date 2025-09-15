<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    protected $fillable = [
        'state',
        'title',
        'slug',
        'short_desc',
        'holiday_pdf',
    ];

    protected static function booted(): void
    {
        static::saving(function (Holiday $model) {
            if (blank($model->slug) && filled($model->title)) {
                $model->slug = static::generateUniqueSlug($model->title, $model->id);
            }
            // Remove: date/day/month logic (belongs to HolidayDetail, not Holiday)
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        $query = static::query();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->clone()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function getHolidayPdfUrlAttribute(): ?string
    {
        return $this->holiday_pdf ? asset('storage/' . $this->holiday_pdf) : null;
    }

    public function details(): HasMany
    {
        return $this->hasMany(HolidayDetail::class)->orderBy('sort_order');
    }
}