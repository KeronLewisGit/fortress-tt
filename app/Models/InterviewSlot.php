<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewSlot extends Model
{
    protected $fillable = ['starts_at', 'duration_minutes', 'location', 'capacity', 'notes', 'created_by'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Future slots with at least one free seat. */
    public function scopeAvailable(Builder $q): Builder
    {
        return $q->where('starts_at', '>', now()->addHours(2))
            ->whereRaw('capacity > (select count(*) from applications where applications.interview_slot_id = interview_slots.id)')
            ->withCount('bookings')->orderBy('starts_at');
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at');
    }

    public function endsAt(): CarbonInterface
    {
        return $this->starts_at->copy()->addMinutes($this->duration_minutes);
    }

    public function seatsLeft(): int
    {
        return max(0, $this->capacity - ($this->bookings_count ?? $this->bookings()->count()));
    }

    public function isBookable(): bool
    {
        return $this->starts_at->gt(now()->addHours(2)) && $this->seatsLeft() > 0;
    }
}
