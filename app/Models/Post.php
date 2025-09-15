<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','content','short_description',
        'author_id','category_id','published_date','tags',
        'image','meta_image','meta_title','meta_description','meta_keywords',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_date' => 'date',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Auto-slug fallback if not provided
    protected static function booted(): void
    {
        static::saving(function ($model) {
            $base = filled($model->slug) ? $model->slug : $model->title;
            $model->slug = static::makeUniqueSlug($base, $model->getKey());
        });
    }
    
    protected static function makeUniqueSlug(string $value, $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $i = 1;
    
        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }
    
        return $slug;
    }
}
