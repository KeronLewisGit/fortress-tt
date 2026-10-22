<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = ['name', 'subject', 'body', 'set_status', 'sort_order'];

    /** Placeholders available in subject/body. */
    public static function placeholders(): array
    {
        return ['{first_name}', '{last_name}', '{job_title}', '{reference}', '{interview_date}', '{company}', '{recruiter}'];
    }

    public static function render(string $text, Application $a, ?User $sender = null): string
    {
        return strtr($text, [
            '{first_name}' => $a->first_name,
            '{last_name}' => $a->last_name,
            '{job_title}' => $a->job?->title ?? 'a position with Fortress Security Services',
            '{reference}' => $a->reference,
            '{interview_date}' => $a->interview_at?->format('l j F Y \a\t g:i a') ?? '[interview date]',
            '{company}' => fortress('name'),
            '{recruiter}' => $sender?->name ?? 'The Recruitment Team',
        ]);
    }
}
