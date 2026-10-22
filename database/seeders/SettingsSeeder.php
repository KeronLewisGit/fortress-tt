<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'business.name' => 'Fortress Security Services Ltd',
            'business.phone' => '(868) 223-2223',
            'business.phone_alt' => '(868) 223-2226',
            'business.whatsapp' => '',
            'business.email' => 'support@fortress.tt',
            'business.recruitment_email' => 'recruitment@fortress.tt',
            'business.address' => '8 Desaine Street, St. Augustine, Trinidad & Tobago',
            'business.hours' => 'Mon – Fri 8:00 am – 5:00 pm · Monitoring centre 24/7',
            'business.facebook' => 'https://www.facebook.com/fortresstt',
            'business.linkedin' => 'https://tt.linkedin.com/in/fortress-sercurity-services-ltd-b71184207',
            'business.x' => 'https://x.com/fortressltd',
            'business.tracking_url' => 'http://track.fortress.tt',
            'notifications.recruitment_email' => 'recruitment@fortress.tt',
            'notifications.enquiry_email' => 'support@fortress.tt',
            'careers.intro' => 'Join a professional team committed to safety, integrity and excellence. We train in-house, promote from within and offer one of the most complete benefit packages in the industry.',
            'careers.general_applications' => '1',
            'careers.auto_reply' => '1',
            'home.announcement' => '',
        ];
        foreach ($defaults as $k => $v) {
            Setting::firstOrCreate(['key' => $k], ['value' => $v]);
        }
        Cache::forget('settings.all');
    }
}
