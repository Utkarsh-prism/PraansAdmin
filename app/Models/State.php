<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class State extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (blank($m->slug)) {
                $m->slug = static::uniqueSlug($m->name);
            }
        });

        static::updating(function (self $m) {
            if ($m->isDirty('name') && blank($m->slug)) {
                $m->slug = static::uniqueSlug($m->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'state';
        $slug = $base;
        $n = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }
        return $slug;
    }

    // Scopes
    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('name'); }
}