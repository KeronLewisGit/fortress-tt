<?php

namespace App\Models;

use App\Notifications\ApplicantResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Applicant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'password', 'resume_path', 'resume_name', 'resume_updated_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'last_login_at' => 'datetime', 'resume_updated_at' => 'datetime'];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class)->latest();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ApplicantNotification::class)->latest();
    }

    public function unreadCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function initials(): string
    {
        return strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->last_name, 0, 1));
    }

    /** Attach applications submitted with this email before the account existed. */
    public function claimApplications(): int
    {
        return Application::whereNull('applicant_id')->where('email', $this->email)->update(['applicant_id' => $this->id]);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ApplicantResetPassword($token));
    }
}
