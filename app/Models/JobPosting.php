<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    public const STATUSES = ['draft' => 'Draft', 'open' => 'Open', 'paused' => 'Paused', 'closed' => 'Closed'];

    public const TYPES = ['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'shift' => 'Shift work'];

    public const DEPARTMENTS = ['Operations', 'Cash in Transit', 'Mobile Patrol & Response', 'Electronic Security', 'Monitoring Centre', 'Investigations', 'Administration', 'Marketing & IT'];

    protected $fillable = [
        'slug', 'title', 'department', 'location', 'employment_type', 'summary', 'description', 'responsibilities', 'requirements',
        'benefits', 'salary_note', 'openings', 'status', 'is_featured', 'requires_licence', 'requires_precept', 'closes_at', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'responsibilities' => 'array', 'requirements' => 'array', 'benefits' => 'array',
            'is_featured' => 'boolean', 'requires_licence' => 'boolean', 'requires_precept' => 'boolean',
            'closes_at' => 'date', 'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Publicly visible: open, published, and not past the closing date. */
    public function scopeLive(Builder $q): Builder
    {
        return $q->where('status', 'open')
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->where(fn ($w) => $w->whereNull('closes_at')->orWhere('closes_at', '>=', today()));
    }

    public function isLive(): bool
    {
        return $this->status === 'open' && $this->published_at && $this->published_at->lte(now())
            && (! $this->closes_at || $this->closes_at->gte(today()));
    }

    public function isClosingSoon(): bool
    {
        return $this->closes_at && $this->closes_at->diffInDays(today(), false) >= -7 && $this->closes_at->gte(today());
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->employment_type] ?? ucfirst($this->employment_type);
    }

    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', (string) $this->description))));
    }
}
