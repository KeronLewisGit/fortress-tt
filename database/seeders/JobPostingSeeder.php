<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starter vacancies based on the roles Fortress has been advertising on Facebook
 * (security drivers, baton officers, officers for Tobago and Point Lisas, EMT
 * officers, social media manager, web developer). Edit or close them in Admin → Jobs.
 */
class JobPostingSeeder extends Seeder
{
    public function run(): void
    {
        $common = [
            'Valid Police Certificate of Character (issued within the last 6 months)',
            'Must be physically fit and capable of performing all assigned duties',
            'Must be punctual, disciplined and professional',
            'Ability to work flexible hours, including weekends and public holidays',
            'Must successfully pass a pre-employment drug screening',
        ];
        $benefits = fortress('benefits');

        $jobs = [
            [
                'title' => 'Security Drivers', 'department' => 'Cash in Transit', 'location' => 'East-West Corridor', 'employment_type' => 'full_time', 'is_featured' => true, 'requires_licence' => true,
                'summary' => 'Drive and support armoured and escort vehicles for our Cash in Transit and courier operations. Defensive-driving certified, Class 4+ permit holders aged 25 and over.',
                'description' => "Join the Fortress Security Services team and be part of a professional team committed to safety, integrity and excellence.\n\nSecurity Drivers operate our CIT and escort fleet along the East-West Corridor, working with a crew commander and our 24-hour monitoring centre on every movement.",
                'responsibilities' => ['Safely operate armoured and escort vehicles on planned routes', 'Carry out pre-trip inspections and report defects', 'Maintain radio contact with the Central Monitoring Station', 'Follow CIT loading, unloading and site-security procedures', 'Complete daily movement logs and incident reports'],
                'requirements' => array_merge(['Must be 25 years and older', 'Minimum of five (5) years driving experience', 'Valid Class 4 (or higher) driver\'s permit', 'Must be able to drive manual-transmission vehicles', 'Defensive Driving Certificate'], $common),
                'benefits' => $benefits, 'openings' => 4,
            ],
            [
                'title' => 'Male Baton Officers – East-West Corridor', 'department' => 'Operations', 'location' => 'East-West Corridor', 'employment_type' => 'shift', 'is_featured' => true,
                'summary' => 'Experienced baton officers for commercial and industrial sites between Port of Spain and Arima. Minimum 3 years in the security field.',
                'description' => "Join the Fortress Security Services team and be part of a professional team committed to safety, integrity and excellence.\n\nBaton officers staff static posts and access points at commercial, industrial and residential sites, supported by our roving supervisors and response teams.",
                'responsibilities' => ['Control access to the site and screen visitors and vehicles', 'Conduct foot patrols and report hazards and incidents', 'Maintain the occurrence book and shift reports', 'Respond to alarms and emergencies in line with site orders', 'Deliver courteous customer service to clients and the public'],
                'requirements' => array_merge(['Minimum of 3 years\' experience in the security field', 'School Leaving Certificate', 'Good command of the English language'], $common),
                'benefits' => $benefits, 'openings' => 10,
            ],
            [
                'title' => 'Security Officers – Tobago', 'department' => 'Operations', 'location' => 'Scarborough and environs, Tobago', 'employment_type' => 'full_time',
                'summary' => 'Officers resident in Tobago for static and patrol posts in Scarborough and environs.',
                'description' => 'Join the Fortress Security Services team in Tobago. Successful applicants receive in-house Level 1 training and a uniform kit, with permanent employment after probation.',
                'responsibilities' => ['Static guarding and access control', 'Foot and vehicle patrols', 'Incident and occurrence reporting', 'Client liaison and customer service'],
                'requirements' => array_merge(['Applicants must reside in Tobago', 'Minimum of 3 years\' experience in the security field', 'School Leaving Certificate', 'Good command of the English language'], $common),
                'benefits' => $benefits, 'openings' => 6,
            ],
            [
                'title' => 'Baton & Precepted Officers – Point Lisas', 'department' => 'Operations', 'location' => 'Point Lisas Industrial Estate', 'employment_type' => 'shift', 'is_featured' => true, 'requires_precept' => true,
                'summary' => 'Baton officers, precepted (armed) officers and Class 3+ drivers for industrial estate contracts. A valid PLEA pass is mandatory; applicants must reside within the Couva / Point Lisas area.',
                'description' => "Positions available at Point Lisas: Baton Officers, Precepted Officers and Drivers (a Class 5 permit would be an asset).\n\nApplicants must reside within the Couva / Point Lisas area and hold a valid PLEA (Point Lisas Energy Association) pass. STOW-certified environment.",
                'responsibilities' => ['Gatehouse and perimeter security on an industrial estate', 'Vehicle and personnel screening', 'Emergency response per site HSE procedures', 'Daily logs and shift handovers'],
                'requirements' => array_merge(['Valid PLEA pass is mandatory', 'Applicants must reside within the Couva / Point Lisas area', 'Precepted Officers: valid Precept and Firearm User\'s Licence', 'Drivers: Class 3 permit minimum (Class 5 an asset)'], $common),
                'benefits' => $benefits, 'openings' => 8,
            ],
            [
                'title' => 'Part-time Security EMT Officers', 'department' => 'Mobile Patrol & Response', 'location' => 'Trinidad – island-wide', 'employment_type' => 'part_time',
                'summary' => 'Security officers with EMT / first-responder certification for events and industrial standby.',
                'description' => 'Fortress Security Services is seeking qualified Security EMT Officers to join our team on a part-time basis, delivering emergency medical care and security at events and client sites.',
                'responsibilities' => ['Provide first-responder medical care on site', 'Support event security and crowd management', 'Maintain medical kit inventory and incident records'],
                'requirements' => array_merge(['Valid EMT or Emergency First Responder certification', 'Security experience is an asset'], $common),
                'benefits' => ['Competitive hourly rates', 'Flexible scheduling', 'In-house training'], 'openings' => 5,
            ],
            [
                'title' => 'Social Media Manager', 'department' => 'Marketing & IT', 'location' => 'St. Augustine (hybrid)', 'employment_type' => 'full_time',
                'summary' => 'Create engaging content, grow our online presence, engage with our community and analyse performance across Facebook, Instagram, LinkedIn and TikTok.',
                'description' => "We're looking for a Social Media Manager to own the Fortress brand online – from recruitment campaigns to service promotion and community engagement.",
                'responsibilities' => ['Create and schedule engaging content', 'Grow our online presence across platforms', 'Engage with our community and respond to enquiries', 'Analyse performance and report results'],
                'requirements' => ['2+ years managing business social media accounts', 'Strong copywriting and basic design skills (Canva / Adobe)', 'Experience with paid social campaigns', 'Portfolio of previous work'],
                'benefits' => $benefits, 'openings' => 1,
            ],
            [
                'title' => 'Web Developer / Digital Systems Administrator', 'department' => 'Marketing & IT', 'location' => 'St. Augustine', 'employment_type' => 'contract',
                'summary' => 'Maintain the Fortress website, customer portals and internal systems; manage users, updates and backups.',
                'description' => 'Manage our websites, GPS customer portal integrations and internal digital systems, including user administration, updates, backups and vendor coordination.',
                'responsibilities' => ['Maintain and improve fortress.tt and the recruitment platform', 'Administer users, updates and backups', 'Coordinate with hosting and software vendors', 'Document systems and train staff'],
                'requirements' => ['Experience with PHP / Laravel, HTML/CSS and MySQL', 'Working knowledge of cPanel / shared hosting and DNS', 'Attention to security best practices', 'Portfolio or GitHub profile'],
                'benefits' => ['Competitive remuneration', 'Flexible working arrangements'], 'salary_note' => 'Negotiable, based on experience', 'openings' => 1,
            ],
        ];

        foreach ($jobs as $i => $job) {
            JobPosting::firstOrCreate(['slug' => Str::slug($job['title'])], $job + [
                'status' => 'open', 'published_at' => now()->subDays(14 - $i), 'closes_at' => today()->addDays(30 + $i * 3),
            ]);
        }
    }
}
