<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public const KEYS = [
        'business.name', 'business.phone', 'business.phone_alt', 'business.whatsapp', 'business.email', 'business.recruitment_email', 'business.address', 'business.hours',
        'business.facebook', 'business.linkedin', 'business.x', 'business.instagram', 'business.tracking_url',
        'notifications.recruitment_email', 'notifications.enquiry_email',
        'careers.intro', 'careers.general_applications', 'careers.auto_reply', 'home.announcement',
    ];

    public function edit()
    {
        return view('admin.settings', ['values' => Setting::all_cached()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.business.name' => ['required', 'string', 'max:120'],
            'settings.business.email' => ['nullable', 'email'],
            'settings.business.recruitment_email' => ['nullable', 'email'],
            'settings.notifications.recruitment_email' => ['nullable', 'string', 'max:500'],
            'settings.notifications.enquiry_email' => ['nullable', 'string', 'max:500'],
            'settings.business.facebook' => ['nullable', 'url'],
            'settings.business.linkedin' => ['nullable', 'url'],
            'settings.business.x' => ['nullable', 'url'],
            'settings.business.instagram' => ['nullable', 'url'],
            'settings.business.tracking_url' => ['nullable', 'url'],
            'settings.careers.intro' => ['nullable', 'string', 'max:1000'],
            'settings.home.announcement' => ['nullable', 'string', 'max:200'],
        ]);

        $flat = [];
        foreach (self::KEYS as $key) {
            $flat[$key] = data_get($data['settings'], $key);
        }
        $flat['careers.general_applications'] = $request->boolean('settings.careers.general_applications') ? '1' : '0';
        $flat['careers.auto_reply'] = $request->boolean('settings.careers.auto_reply') ? '1' : '0';
        Setting::setMany($flat);

        return back()->with('status', 'Settings saved.');
    }
}
