<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    /** Pipeline stages in order. */
    public const STAGES = [
        'new' => 'New', 'screening' => 'Screening', 'interview' => 'Interview', 'assessment' => 'Assessment & Vetting',
        'offer' => 'Offer', 'hired' => 'Hired', 'rejected' => 'Rejected', 'withdrawn' => 'Withdrawn',
    ];

    public const ACTIVE_STAGES = ['new', 'screening', 'interview', 'assessment', 'offer'];

    public const LICENCES = ['none' => 'No permit', '3' => 'Class 3', '4' => 'Class 4', '5' => 'Class 5', 'ht' => 'Heavy-T'];

    public const SOURCES = ['website' => 'Website', 'facebook' => 'Facebook', 'referral' => 'Referral', 'walk_in' => 'Walk-in', 'linkedin' => 'LinkedIn', 'other' => 'Other'];

    protected $fillable = [
        'reference', 'job_posting_id', 'first_name', 'last_name', 'email', 'phone', 'address', 'area', 'date_of_birth', 'nationality',
        'licence_class', 'has_precept', 'has_police_certificate', 'has_firearm_licence', 'years_experience', 'education', 'current_employer',
        'cover_letter', 'resume_path', 'resume_name', 'source', 'status', 'rating', 'assigned_to', 'status_changed_at', 'interview_at', 'consent', 'ip',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date', 'has_precept' => 'boolean', 'has_police_certificate' => 'boolean', 'has_firearm_licence' => 'boolean',
            'consent' => 'boolean', 'status_changed_at' => 'datetime', 'interview_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $a) {
            $a->reference ??= self::generateReference();
            $a->status_changed_at ??= now();
        });
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'FSS-'.strtoupper(Str::random(6));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class)->latest();
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', self::ACTIVE_STAGES);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $q;
        }

        return $q->where(function ($w) use ($term) {
            $like = '%'.$term.'%';
            $w->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like)->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)->orWhere('reference', 'like', $like)->orWhere('area', 'like', $like)
                ->orWhereRaw("(first_name || ' ' || last_name) like ?", [$like]);
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->status] ?? ucfirst($this->status);
    }

    public function age(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function licenceLabel(): string
    {
        return self::LICENCES[$this->licence_class] ?? 'Not stated';
    }

    public function initials(): string
    {
        return strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->last_name, 0, 1));
    }

    /** Move to a stage and write a timeline entry. */
    public function moveTo(string $status, ?User $by = null, ?string $comment = null): void
    {
        if (! array_key_exists($status, self::STAGES) || $status === $this->status) {
            return;
        }
        $from = $this->status;
        $this->forceFill(['status' => $status, 'status_changed_at' => now()])->save();
        $this->notes()->create([
            'user_id' => $by?->id,
            'type' => 'status',
            'body' => $comment ?: 'Moved from '.(self::STAGES[$from] ?? $from).' to '.self::STAGES[$status],
            'meta' => ['from' => $from, 'to' => $status],
        ]);
    }
}
