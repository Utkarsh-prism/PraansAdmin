<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ActRuleForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'act_desc',
        'rule_desc',
        'state',
        'upload_path',
        'form_no',
        'form_title',
        'form_short_desc',
        'form_image_path',
        'form_pdf_path',
        'section_count',
        'rule_count',
        'form_count',
    ];


    protected static function booted(): void
    {
        // On create → set slug if empty
        static::creating(function (self $m) {
            if (blank($m->slug) && filled($m->title)) {
                $m->slug = static::uniqueSlug($m->title);
            }
        });

        // On update → if title changed and slug is blank, (re)create
        static::updating(function (self $m) {
            if ($m->isDirty('title') && blank($m->slug)) {
                $m->slug = static::uniqueSlug($m->title);
            }
        });
    }

    /** Make a unique slug from title. */
    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'item';
        $slug = $base;
        $n = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }
        return $slug;
    }

    public function forms(): HasMany
    {
        return $this->hasMany(ActForm::class)->orderBy('sort_order');
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    protected $casts = [
        // NEW
        'section_count' => 'integer',
        'rule_count'    => 'integer',
        'form_count'    => 'integer',
    ];
}
