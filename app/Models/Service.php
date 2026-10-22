<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public const ICONS = ['shield', 'truck', 'camera', 'bell', 'user-shield', 'map-pin', 'package', 'search', 'calendar', 'car', 'activity', 'lock', 'radio', 'eye'];

    protected $fillable = ['slug', 'name', 'eyebrow', 'icon', 'summary', 'description', 'features', 'ideal_for', 'is_featured', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['features' => 'array', 'ideal_for' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', (string) $this->description))));
    }
}
