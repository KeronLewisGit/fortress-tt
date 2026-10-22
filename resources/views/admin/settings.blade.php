<x-layouts.admin title="Settings">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-6 lg:grid-cols-2">@csrf @method('PUT')
        @php $v = fn ($k, $d = '') => old('settings.'.$k, $values[$k] ?? $d); @endphp
        <div class="admin-card p-6 space-y-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Business details</h2>
            <div><label class="label">Business name *</label><input name="settings[business][name]" value="{{ $v('business.name') }}" required class="field"></div>
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="label">Main phone</label><input name="settings[business][phone]" value="{{ $v('business.phone') }}" class="field"></div><div><label class="label">Alternate phone</label><input name="settings[business][phone_alt]" value="{{ $v('business.phone_alt') }}" class="field"></div></div>
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="label">WhatsApp number</label><input name="settings[business][whatsapp]" value="{{ $v('business.whatsapp') }}" class="field" placeholder="868-"><p class="help">Shows a WhatsApp button on the contact page when set.</p></div><div><label class="label">Vehicle tracking URL</label><input name="settings[business][tracking_url]" value="{{ $v('business.tracking_url') }}" class="field"></div></div>
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="label">General email</label><input type="email" name="settings[business][email]" value="{{ $v('business.email') }}" class="field"></div><div><label class="label">Recruitment email (public)</label><input type="email" name="settings[business][recruitment_email]" value="{{ $v('business.recruitment_email') }}" class="field"></div></div>
            <div><label class="label">Head office address</label><input name="settings[business][address]" value="{{ $v('business.address') }}" class="field"></div>
            <div><label class="label">Opening hours</label><input name="settings[business][hours]" value="{{ $v('business.hours') }}" class="field"></div>
        </div>
        <div class="space-y-6">
            <div class="admin-card p-6 space-y-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Notifications</h2>
                <div><label class="label">New application alerts go to</label><input name="settings[notifications][recruitment_email]" value="{{ $v('notifications.recruitment_email') }}" class="field" placeholder="recruitment@fortress.tt, hr@fortress.tt"><p class="help">Separate several addresses with commas.</p></div>
                <div><label class="label">Quote enquiry alerts go to</label><input name="settings[notifications][enquiry_email]" value="{{ $v('notifications.enquiry_email') }}" class="field"></div>
            </div>
            <div class="admin-card p-6 space-y-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Careers page</h2>
                <div><label class="label">Intro text</label><textarea name="settings[careers][intro]" rows="3" class="field">{{ $v('careers.intro') }}</textarea></div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="settings[careers][general_applications]" value="1" @checked($v('careers.general_applications', '1') === '1') class="check">Accept general applications (no specific vacancy)</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="settings[careers][auto_reply]" value="1" @checked($v('careers.auto_reply', '1') === '1') class="check">Email applicants an automatic confirmation with their reference</label>
            </div>
            <div class="admin-card p-6 space-y-4"><h2 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Social &amp; home page</h2>
                <div class="grid gap-4 sm:grid-cols-2"><div><label class="label">Facebook</label><input name="settings[business][facebook]" value="{{ $v('business.facebook') }}" class="field"></div><div><label class="label">LinkedIn</label><input name="settings[business][linkedin]" value="{{ $v('business.linkedin') }}" class="field"></div><div><label class="label">X / Twitter</label><input name="settings[business][x]" value="{{ $v('business.x') }}" class="field"></div><div><label class="label">Instagram</label><input name="settings[business][instagram]" value="{{ $v('business.instagram') }}" class="field"></div></div>
                <div><label class="label">Home page announcement</label><input name="settings[home][announcement]" value="{{ $v('home.announcement') }}" class="field" maxlength="200" placeholder="e.g. Now hiring officers in Tobago – apply by 30 Nov"><p class="help">Short banner in the hero. Leave blank to hide.</p></div>
            </div>
            <button class="admin-btn-primary">Save settings</button>
        </div>
    </form>
</x-layouts.admin>
