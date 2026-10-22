<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Invite to interview', 'set_status' => 'interview', 'subject' => 'Interview invitation – {job_title} ({reference})',
                'body' => "Dear {first_name},\n\nThank you for applying for the position of {job_title} with {company}. We would like to invite you to an interview on {interview_date} at our head office, 8 Desaine Street, St. Augustine.\n\nPlease walk with:\n• Two forms of national identification\n• Your Police Certificate of Character\n• Driver's permit (if applicable)\n• Original certificates and a passport-size photo\n\nReply to this email or call (868) 223-2223 to confirm your attendance.\n\nRegards,\n{recruiter}\nRecruitment – {company}"],
            ['name' => 'Request documents', 'set_status' => 'screening', 'subject' => 'Documents required – application {reference}',
                'body' => "Dear {first_name},\n\nThank you for your application for {job_title}. To move to the next stage we need copies of the following:\n\n• Police Certificate of Character (issued within the last 6 months)\n• National ID and birth certificate\n• Driver's permit (if you hold one)\n• School leaving certificate\n\nYou can reply to this email with photos or scans. Your reference is {reference}.\n\nRegards,\n{recruiter}\n{company}"],
            ['name' => 'Assessment & vetting', 'set_status' => 'assessment', 'subject' => 'Next steps – aptitude test and vetting ({reference})',
                'body' => "Dear {first_name},\n\nCongratulations – you have progressed to the assessment stage for {job_title}. You will be scheduled for our aptitude test (written English, practical knowledge and physical fitness) and a pre-employment drug screening. Background checks will also be carried out in-house.\n\nWe will contact you by phone to confirm the date.\n\nRegards,\n{recruiter}\n{company}"],
            ['name' => 'Offer of employment', 'set_status' => 'offer', 'subject' => 'Offer of employment – {job_title}',
                'body' => "Dear {first_name},\n\nWe are pleased to offer you the position of {job_title} with {company}. Please visit our head office at 8 Desaine Street, St. Augustine to collect your offer letter, complete onboarding documents and be measured for your uniform kit.\n\nYour Level 1 training class start date will be confirmed at that time.\n\nWelcome to the team,\n{recruiter}\n{company}"],
            ['name' => 'Unsuccessful', 'set_status' => 'rejected', 'subject' => 'Your application – {job_title}',
                'body' => "Dear {first_name},\n\nThank you for your interest in {company} and for the time you invested in your application for {job_title}. After careful consideration we will not be progressing your application on this occasion.\n\nWe keep applications on file for six months and encourage you to apply again for future openings at fortress.tt/careers.\n\nRegards,\n{recruiter}\n{company}"],
        ];
        foreach ($rows as $i => $r) {
            MessageTemplate::firstOrCreate(['name' => $r['name']], $r + ['sort_order' => $i]);
        }
    }
}
