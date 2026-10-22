<?php

namespace App\Models;

use App\Services\ApplicantNotifier;
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

    /** What the applicant sees in their portal for each internal stage. */
    public const PUBLIC_STATUSES = [
        'new' => ['sent', 'Application sent'], 'screening' => ['review', 'In review'], 'assessment' => ['review', 'In review'],
        'interview' => ['interview', 'Interview stage'], 'offer' => ['accepted', 'Accepted'], 'hired' => ['accepted', 'Accepted'],
        'rejected' => ['rejected', 'Not successful'], 'withdrawn' => ['withdrawn', 'Withdrawn'],
    ];

    public const SOURCES = ['website' => 'Website', 'facebook' => 'Facebook', 'referral' => 'Referral', 'walk_in' => 'Walk-in', 'linkedin' => 'LinkedIn', 'other' => 'Other'];

    protected $fillable = [
        'reference', 'job_posting_id', 'applicant_id', 'first_name', 'last_name', 'email', 'phone', 'address', 'area', 'date_of_birth', 'nationality',
        'licence_class', 'has_precept', 'has_police_certificate', 'has_firearm_licence', 'years_experience', 'education', 'current_employer',
        'cover_letter', 'resume_path', 'resume_name', 'source', 'status', 'rating', 'assigned_to', 'status_changed_at', 'interview_at', 'interview_slot_id', 'interview_location', 'consent', 'ip',
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

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function interviewSlot(): BelongsTo
    {
        return $this->belongsTo(InterviewSlot::class);
    }

    public function publicStatus(): string
    {
        return self::PUBLIC_STATUSES[$this->status][0] ?? 'sent';
    }

    public function publicStatusLabel(): string
    {
        return self::PUBLIC_STATUSES[$this->status][1] ?? 'Application sent';
    }

    /** Applicant may pick an interview slot when invited to interview and no date is fixed yet. */
    public function canBookInterview(): bool
    {
        return $this->status === 'interview' && ! $this->interview_at;
    }

    /** Timeline entries safe to show the applicant (no internal recruiter notes). */
    public function publicNotes()
    {
        return $this->notes()->whereIn('type', ['status', 'interview', 'email', 'system'])
            ->where(fn ($q) => $q->where('type', '!=', 'system')->orWhere('body', 'like', 'Application received%')->orWhere('body', 'like', 'Interview booked%'));
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

        $like = '%'.$term.'%';
        $words = preg_split('/\s+/', $term);

        return $q->where(function ($w) use ($like, $words) {
            $w->where('email', 'like', $like)->orWhere('phone', 'like', $like)->orWhere('reference', 'like', $like)->orWhere('area', 'like', $like)
                // "Kerry Joseph" → every word must match a name part (portable, no DB-specific concat)
                ->orWhere(function ($n) use ($words) {
                    foreach ($words as $word) {
                        $n->where(fn ($p) => $p->where('first_name', 'like', '%'.$word.'%')->orWhere('last_name', 'like', '%'.$word.'%'));
                    }
                });
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
            'body' => 'Moved from '.(self::STAGES[$from] ?? $from).' to '.self::STAGES[$status].($comment ? ' — '.trim($comment) : ''),
            'meta' => ['from' => $from, 'to' => $status],
        ]);

        if ((self::PUBLIC_STATUSES[$from][0] ?? null) !== $this->publicStatus()) {
            app(ApplicantNotifier::class)->statusChanged($this);
        }
    }
}
