<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GazetteNortification extends Model
{
    protected $table = 'gazette_nortifications';
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'state',
        'updated_date',
        'effective_date',
        'description',
        'pdf_path',
    ];

    protected $casts = [
        'updated_date'   => 'date',
        'effective_date' => 'date',
    ];
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (blank($m->slug)) {
                $m->slug = static::uniqueSlug($m->title);
            }
        });

        // If title changes and slug left blank (or matches old title’s slug), refresh it
        static::updating(function (self $m) {
            if ($m->isDirty('title') && (blank($m->slug) ||
                $m->getOriginal('slug') === Str::slug($m->getOriginal('title')))) {
                $m->slug = static::uniqueSlug($m->title, $m->getKey());
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'gazette';
        $slug = $base;
        $n = 2;

        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }

    // Optional: use slugs in route model binding
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
