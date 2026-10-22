<?php

// Service catalogue – copy adapted from fortress.tt/services.php and the Facebook service wheel.
return [
    [
        'slug' => 'protection-officers', 'name' => 'Protection Officers', 'eyebrow' => 'Manned guarding', 'icon' => 'shield', 'is_featured' => true,
        'summary' => 'Armed and unarmed officers, baton officers, K9 teams, front-desk sentries and mobile patrols – trained in-house and supervised around the clock.',
        'description' => "Fortress offers a wide range of efficient and professional manned security services. Every officer completes our in-house Level 1 programme before deployment and is supervised by a roving field team, with management available 24 hours a day.\n\nWe match the officer to the post: firearm and baton officers for high-risk sites, K9 handlers for perimeter and industrial estates, sentry and front-desk officers for corporate receptions, and tactical response teams backed by our Central Monitoring Station.",
        'features' => ['Firearm (precepted) and baton officers', 'Stationary and static guards', 'K9 officers and handlers', 'Sentry / front-desk security', 'Mobile patrol and tactical response teams', 'Roving supervisors and incident reporting'],
        'ideal_for' => ['Commercial and industrial estates', 'Residential communities and gated compounds', 'Banks, retail and warehouses', 'Schools, churches and NGOs'],
    ],
    [
        'slug' => 'cash-in-transit', 'name' => 'Cash in Transit', 'eyebrow' => 'Secure logistics', 'icon' => 'truck', 'is_featured' => true,
        'summary' => 'Secure, reliable transportation of funds and valuables between banks, businesses and ATMs by Level 3 certified, polygraph-vetted crews.',
        'description' => "Our Cash in Transit service provides secure and reliable transportation of funds to and from any location – banks, businesses, retail outlets and individuals.\n\nCIT crews complete our Level 3 programme, which adds credit checks, ongoing background checks, polygraph testing, evasive driving and tactical weapons training. Every movement is tracked live by our monitoring centre.",
        'features' => ['Cash transport and cash management', 'ATM replenishment and first-line maintenance', 'Financial institution outsourcing', 'International valuables transport', 'GPS-tracked armoured movements', 'Full chain-of-custody documentation'],
        'ideal_for' => ['Banks and credit unions', 'Supermarkets and retail chains', 'Fuel stations and casinos', 'Payroll and government agencies'],
    ],
    [
        'slug' => 'personal-protection', 'name' => 'Personal & VIP Protection', 'eyebrow' => 'Executive protection', 'icon' => 'user-shield', 'is_featured' => true,
        'summary' => 'Discreet close-protection and escort officers for diplomats, executives, government officials and their families.',
        'description' => "Our personal protection officers are highly trained and specifically selected for the task at hand. They protect diplomats, government officials, executives and family members at home, in transit and at public engagements.\n\nAssignments are planned in advance with route surveys, venue advances and secure transport, and every officer completes our Response Officer programme in VIP and executive protection.",
        'features' => ['Executive bodyguards and close protection', 'Escort and secure transport', 'Residential protection details', 'Advance work and threat assessments', 'Event and travel security'],
        'ideal_for' => ['Diplomatic missions', 'C-suite executives and business owners', 'Visiting artistes and delegations', 'High-net-worth families'],
    ],
    [
        'slug' => 'cctv-access-control-alarms', 'name' => 'CCTV, Access Control & Alarms', 'eyebrow' => 'Electronic security', 'icon' => 'camera', 'is_featured' => true,
        'summary' => 'Smart surveillance, keypad, card and biometric access control, hard-wired or wireless burglary and fire alarms – designed, installed and maintained.',
        'description' => "Our electronic security division designs, installs and maintains camera packages sized to your premises – from a family home to a multi-site business – alongside access control using keypads, swipe cards, proximity readers and biometrics.\n\nBurglary alarm systems combine a control panel, motion sensors and door/window contacts, with alerts by SMS, email and our 24-hour Central Monitoring Station. Wireless (SIM-based) systems, smoke detectors tied to the alarm panel, panic buttons, electronic locks and automated gates and doors complete the package.",
        'features' => ['HD / IP CCTV and remote viewing', 'Keypad, card, proximity and biometric access', 'Hard-wired and wireless burglary alarms', 'Fire alarms and smoke detection', 'Panic buttons and electronic locks', 'Automated residential and commercial gates'],
        'ideal_for' => ['Homes and apartment complexes', 'Offices and retail', 'Warehouses and factories', 'Schools and places of worship'],
    ],
    [
        'slug' => 'alarm-monitoring', 'name' => '24-Hour Alarm Monitoring', 'eyebrow' => 'Central Monitoring Station', 'icon' => 'bell',
        'summary' => 'Professional monitoring 24 hours a day, 365 days a year from a station with multiple backups that keeps operating through hurricanes, blackouts and floods.',
        'description' => "All of our monitoring packages include professional monitoring 24 hours a day, 7 days a week and 365 days a year. When your system is triggered, our operators alert you, dispatch our response team and notify the emergency services.\n\nOur monitoring centre runs on multiple backup systems so it continues to operate during hurricanes, blackouts, power failures and flooding.",
        'features' => ['24/7/365 live operators', 'Response-team dispatch', 'Emergency-services notification', 'Redundant power and communications', 'Monthly activity reports'],
        'ideal_for' => ['Any Fortress-installed alarm or CCTV system', 'Existing systems from other installers', 'Remote and rural properties'],
    ],
    [
        'slug' => 'gps-tracking', 'name' => 'GPS Tracking (Fortress Global)', 'eyebrow' => 'Fleet & asset tracking', 'icon' => 'map-pin',
        'summary' => 'Web-based real-time tracking for personal vehicles and fleets with geofences, routing tools, rich reporting and SMS / email alerts.',
        'description' => "Fortress Global is our web-based GPS tracking platform with a real-time view of a single vehicle or thousands of devices at once – from any PC or mobile device with an internet connection.\n\nCreate circular, polygon or route-following geofences and points of interest, run reports on speed, mileage, alarms, driver behaviour and maintenance schedules, and receive SMS, email and in-app alerts for SOS, theft, excessive speeding or idling. Our response team supports the recovery of stolen vehicles.",
        'features' => ['Real-time and historical tracking', 'Geofences, routes and POIs', 'Driver behaviour and maintenance reports', 'SOS, theft, speed and idle alerts', 'Stolen-vehicle response and recovery', 'Customer portal: track.fortress.tt'],
        'ideal_for' => ['Delivery and service fleets', 'Rental and company cars', 'Personal vehicles and motorcycles', 'Heavy equipment and assets'],
    ],
    [
        'slug' => 'courier-services', 'name' => 'Secure Courier', 'eyebrow' => 'Documents & valuables', 'icon' => 'package',
        'summary' => 'Security-escorted delivery of messages, documents, money and mail – tracked from pick-up to signature.',
        'description' => "We deliver all types of packages – including messages, money and mail – securely and on schedule. Every item is logged, GPS-tracked while in transit and signed for on delivery, so you can follow the package until it reaches the recipient.",
        'features' => ['Same-day island-wide delivery', 'Confidential document handling', 'Cash and cheque collections', 'Proof of delivery and tracking'],
        'ideal_for' => ['Law firms and financial institutions', 'Medical and laboratory services', 'Government and corporate offices'],
    ],
    [
        'slug' => 'investigations', 'name' => 'Private Investigations', 'eyebrow' => 'Discreet & confidential', 'icon' => 'search',
        'summary' => 'Fraud, internal theft, burglary and robbery investigations, surveillance, missing persons and polygraph services.',
        'description' => "Our investigators handle fraud, internal theft, burglary and robbery investigations, surveillance, missing-person enquiries, child custody and domestic matters with discretion and detailed reporting suitable for management or legal use.\n\nPolygraph services are available for pre-employment screening and specific-incident investigations.",
        'features' => ['Fraud and internal theft', 'Burglary and robbery investigations', 'Surveillance and background checks', 'Missing persons', 'Polygraph examinations', 'Court-ready reporting'],
        'ideal_for' => ['Employers and HR departments', 'Insurers and attorneys', 'Private individuals'],
    ],
    [
        'slug' => 'event-security', 'name' => 'Special Event Security', 'eyebrow' => 'Crowd management', 'icon' => 'calendar',
        'summary' => 'Trained crowd-control officers, access screening and incident management for events of any size – from private functions to national festivals.',
        'description' => "We provide security for events of all sizes and ensure every event is well monitored and professionally managed – gate and ticket screening, crowd control, VIP areas, backstage and overnight site security, coordinated with the police and emergency services where required.",
        'features' => ['Crowd control and access screening', 'VIP and artiste protection', 'Overnight site and equipment security', 'Traffic and parking management', 'Liaison with TTPS and emergency services'],
        'ideal_for' => ['Fêtes, concerts and Carnival events', 'Corporate functions and conferences', 'Weddings and private parties', 'Sporting events'],
    ],
    [
        'slug' => 'valet-services', 'name' => 'Valet Services', 'eyebrow' => 'Hospitality', 'icon' => 'car',
        'summary' => 'Uniformed, licensed valet attendants who combine guest service with vehicle security for hotels, restaurants and events.',
        'description' => "Our valet attendants hold valid driver's permits, complete defensive-driving training and are background-checked, giving your guests a professional welcome while vehicles stay under watch.",
        'features' => ['Licensed, insured attendants', 'Keyed ticketing and vehicle condition logging', 'Parking lot patrol', 'Event and permanent contracts'],
        'ideal_for' => ['Hotels and restaurants', 'Private functions and weddings', 'Medical centres and corporate HQs'],
    ],
];
