<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    //  Relationships

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * NOTE: this relation used to `->with('children')` on itself, which
     * eager-loads children of children of children... indefinitely, for
     * every touch of this relation. That's an unbounded recursive query
     * chain — fine for a 2-level tree, dangerous the moment someone adds a
     * 3rd/4th level of nesting.
     *
     * Load depth explicitly at the call site instead, e.g.:
     *   ServiceCategory::with('children.children')->get()
     * or, for typical top-level + one level of children (the current
     * app usage in Public\ServiceController), just:
     *   ServiceCategory::with('children')->get()
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('sort_order');
    }

    public function activeServices(): HasMany
    {
        return $this->services()->active();
    }

    //  Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    //  Helpers

    public function isTopLevel(): bool
    {
        return is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_path . ' > ' . $this->name;
        }
        return $this->name;
    }

    // Auto-generate slug
    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }
}
