<?php

/*
|--------------------------------------------------------------------------
| Fortress Security Services – site content
|--------------------------------------------------------------------------
| Static copy validated against fortress.tt, the company Facebook page
| (facebook.com/fortresstt), the Couva/Point Lisas Chamber directory and
| Findyello (Oct 2026). Editable business details live in the settings table.
*/

return [
    'name' => 'Fortress Security Services Ltd',
    'short_name' => 'Fortress Security',
    'motto' => 'Protection Beyond Your Expectation',
    'tagline' => 'Because we understand the importance of prevention and security in real time.',
    'values_line' => 'Vigilance · Integrity · Professionalism',
    'founded' => 2006,
    'registered_since' => 2005,

    'offices' => [
        [
            'name' => 'Head Office – St. Augustine',
            'address' => '8 Desaine Street, St. Augustine, Trinidad & Tobago',
            'phone' => '(868) 223-2223',
            'phone_alt' => '(868) 223-2226',
            'fax' => '(868) 663-3364',
            'hours' => 'Mon – Fri 8:00 am – 5:00 pm',
            'map' => 'https://www.google.com/maps/search/?api=1&query=Fortress+Security+Services+Ltd+8+Desaine+Street+St+Augustine+Trinidad',
        ],
        [
            'name' => 'South Branch – Marabella',
            'address' => '#98 Amarsingh Street, Marabella, Trinidad & Tobago',
            'phone' => '(868) 223-9666',
            'phone_alt' => null,
            'fax' => null,
            'hours' => 'Mon – Fri 8:00 am – 5:00 pm',
            'map' => 'https://www.google.com/maps/search/?api=1&query=98+Amarsingh+Street+Marabella+Trinidad',
        ],
    ],

    'social' => [
        'facebook' => 'https://www.facebook.com/fortresstt',
        'linkedin' => 'https://tt.linkedin.com/in/fortress-sercurity-services-ltd-b71184207',
        'x' => 'https://x.com/fortressltd',
    ],

    'tracking_url' => 'http://track.fortress.tt',

    'stats' => [
        ['value' => '24/7', 'label' => 'Central monitoring station'],
        ['value' => '2006', 'label' => 'Serving T&T since'],
        ['value' => '2', 'label' => 'Offices – North & South'],
        ['value' => '6.7K+', 'label' => 'Community following'],
    ],

    'certifications' => [
        ['year' => 2014, 'title' => 'IFPO Corporate Member', 'body' => 'Corporate Membership certification from the International Foundation for Protection Officers.'],
        ['year' => 2016, 'title' => 'STOW Certificate of Conformance', 'body' => 'Safe To Work certification recognised across the Trinidad & Tobago energy sector.'],
        ['year' => 2005, 'title' => 'Ministry of National Security', 'body' => 'Officially registered and operating under the Ministry of National Security.'],
    ],

    'timeline' => [
        ['year' => '2005', 'title' => 'Registered', 'body' => 'Registered under the Ministry of National Security as Personnel Protection Services Ltd.'],
        ['year' => '2006', 'title' => 'Operations begin', 'body' => 'Founded in response to rising crime, with a focus on trained, professional protection officers.'],
        ['year' => '2014', 'title' => 'IFPO certification', 'body' => 'Corporate Membership with the International Foundation for Protection Officers.'],
        ['year' => '2016', 'title' => 'STOW certified', 'body' => 'Certificate of Conformance opens the door to energy-sector and industrial estate contracts.'],
        ['year' => 'Today', 'title' => 'Island-wide coverage', 'body' => 'Head office in St. Augustine, South branch in Marabella and teams deployed from Point Lisas to Tobago.'],
    ],

    'mission' => 'To provide our clients with a network of innovative security solutions and technology for a safe environment and more effective integrated security systems, delivered with quality outcomes and consistently superior service.',
    'vision' => 'To become the leading security services provider in Trinidad & Tobago and the wider Caribbean, offering security and technology solutions that build client satisfaction, loyalty and economic value.',
    'commitments' => [
        ['title' => 'Prevent losses', 'body' => 'Deterrence first. Visible, disciplined officers and layered electronic systems stop incidents before they start.'],
        ['title' => 'Protect life, property & information', 'body' => 'From executive protection to confidential investigations, we safeguard what matters most to you.'],
        ['title' => 'Service excellence', 'body' => 'Management is available 24/7. Your account is reviewed regularly and our response is measured in minutes, not days.'],
    ],

    // Entry assessment + training ladder, from fortress.tt/training.php
    'training' => [
        [
            'level' => 'Entry',
            'title' => 'Aptitude assessment',
            'body' => 'Every applicant sits an aptitude test covering written English, practical knowledge and a physical fitness overview before being accepted for training.',
            'topics' => ['Written English', 'Practical knowledge', 'Physical fitness overview', 'In-house background check'],
        ],
        [
            'level' => 'Level 1',
            'title' => 'Foundation security officer',
            'body' => 'Delivered by senior officers. Graduates receive a uniform kit and serve a three-month probation before progressing to Level 2.',
            'topics' => ['Duties of a security officer', 'Public relations & dress code', 'Patrols and fixed posts', 'Diary & report writing', 'Use of force', 'Observation & patrol techniques', 'Safety & fire prevention'],
        ],
        [
            'level' => 'Level 2',
            'title' => 'Advanced officer',
            'body' => 'Builds the judgement needed for commercial and industrial sites.',
            'topics' => ['Traffic & crowd control', 'Strikes and labour relations', 'Security investigations', 'Employee dishonesty', 'Legal aspects of security', 'Stress management', 'Human relations'],
        ],
        [
            'level' => 'Response',
            'title' => 'Response officer',
            'body' => 'For mobile patrol, tactical response and VIP assignments.',
            'topics' => ['Advanced crowd control', 'Explosives & bomb threats', 'Hazardous materials', 'Terrorism & VIP protection', 'Defensive driving', 'Crisis intervention', 'Firearms legislation', 'Control tactics & arrest', 'Executive protection'],
        ],
        [
            'level' => 'Level 3',
            'title' => 'Cash in Transit',
            'body' => 'Additional vetting: credit check, ongoing background checks, polygraph and a valid driver\'s permit.',
            'topics' => ['CIT procedures', 'Loading in unsecured areas', 'Site security', 'Tactical weapons', 'Evasive driving', 'Close combat', 'Client confidentiality'],
        ],
    ],

    // Employee benefits, from fortress.tt/careers.php
    'benefits' => [
        'Competitive wages',
        'Permanent employment',
        'In-house training & certification',
        'Paid sick leave',
        'Health plan & life insurance',
        'Workmen\'s compensation',
        'Pension plan',
        'Vacation & casual leave',
        'Annual bonus package (profit sharing)',
        'Double time on public holidays',
        'Assistance with personal development',
    ],

    // Baseline applicant requirements shown on the careers page
    'applicant_requirements' => [
        'National of Trinidad & Tobago, aged 18 – 50',
        'Physically fit and capable of performing all assigned duties',
        'Valid Police Certificate of Character (issued within the last 6 months)',
        'School leaving certificate and good command of English',
        'Punctual, disciplined and professional',
        'Able to work flexible hours including weekends and public holidays',
        'Must pass a pre-employment drug screening',
        'Driver\'s permit (Class 3 or higher) is an asset for driver and CIT roles',
    ],

    'contact_services' => [
        'Protection Officers', 'Cash in Transit', 'Courier Services', 'Personal / VIP Protection',
        'CCTV, Access Control & Alarms', 'Alarm Monitoring', 'GPS Tracking', 'Investigations',
        'Special Event Security', 'Valet Services', 'Polygraph Services', 'Other',
    ],

    // Licensed Shutterstock imagery (Standard License, team subscription) – public/assets/img/*.webp
    'service_images' => [
        'protection-officers' => 'industrial-team',
        'cash-in-transit' => 'cit-truck',
        'personal-protection' => 'executive-protection',
        'cctv-access-control-alarms' => 'cctv-install',
        'alarm-monitoring' => 'control-room',
        'gps-tracking' => 'gps-fleet',
        'courier-services' => 'uniform-detail',
        'investigations' => 'consultation',
        'event-security' => 'event-gate',
        'valet-services' => 'valet',
    ],

    // First administrator for `php artisan db:seed` (AdminSeeder). Set in .env, remove the password after first sign-in.
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
