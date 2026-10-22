<x-layouts.admin :title="$job->exists ? 'Edit job' : 'Post a job'">
    <x-slot:actions>
        @if($job->exists)
            @if($job->isLive())<a href="{{ route('careers.show', $job) }}" target="_blank" class="admin-btn-secondary">View live ↗</a>@endif
            <a href="{{ route('admin.applications.index', ['job' => $job->id]) }}" class="admin-btn-secondary">{{ $job->applications_count }} applicants</a>
        @endif
    </x-slot:actions>
    <form method="POST" action="{{ $job->exists ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="grid gap-6 lg:grid-cols-3">
        @csrf @if($job->exists)@method('PUT')@endif
        <div class="space-y-6 lg:col-span-2">
            <div class="admin-card p-6 space-y-5">
                <div><label class="label" for="title">Job title *</label><input id="title" name="title" value="{{ old('title', $job->title) }}" required class="field text-lg font-semibold" placeholder="e.g. Male Baton Officers – East-West Corridor"></div>
                <div class="grid gap-5 sm:grid-cols-3">
                    <div><label class="label" for="department">Department</label><input id="department" name="department" list="departments" value="{{ old('department', $job->department) }}" class="field"><datalist id="departments">@foreach(\App\Models\JobPosting::DEPARTMENTS as $d)<option value="{{ $d }}">@endforeach</datalist></div>
                    <div><label class="label" for="location">Location *</label><input id="location" name="location" value="{{ old('location', $job->location) }}" required class="field" placeholder="e.g. Point Lisas"></div>
                    <div><label class="label" for="employment_type">Type *</label><select id="employment_type" name="employment_type" class="field">@foreach(\App\Models\JobPosting::TYPES as $k => $v)<option value="{{ $k }}" @selected(old('employment_type', $job->employment_type) === $k)>{{ $v }}</option>@endforeach</select></div>
                </div>
                <div><label class="label" for="summary">Summary * <span class="font-normal text-steel-500">(shown on cards and in search results)</span></label><textarea id="summary" name="summary" rows="2" maxlength="600" required class="field">{{ old('summary', $job->summary) }}</textarea></div>
                <div><label class="label" for="description">Description</label><textarea id="description" name="description" rows="6" class="field" placeholder="Separate paragraphs with a blank line.">{{ old('description', $job->description) }}</textarea></div>
            </div>
            <div class="admin-card p-6 grid gap-5 md:grid-cols-3">
                <div><label class="label" for="responsibilities">Responsibilities</label><textarea id="responsibilities" name="responsibilities" rows="9" class="field text-sm" placeholder="One per line">{{ old('responsibilities', implode("\n", $job->responsibilities ?? [])) }}</textarea></div>
                <div><label class="label" for="requirements">Minimum requirements</label><textarea id="requirements" name="requirements" rows="9" class="field text-sm" placeholder="One per line">{{ old('requirements', implode("\n", $job->requirements ?? [])) }}</textarea></div>
                <div><label class="label" for="benefits">Benefits</label><textarea id="benefits" name="benefits" rows="9" class="field text-sm" placeholder="One per line">{{ old('benefits', implode("\n", $job->benefits ?? [])) }}</textarea></div>
                <p class="md:col-span-3 help">Enter one item per line. Leading dashes or bullets are removed automatically.</p>
            </div>
        </div>
        <aside class="space-y-6">
            <div class="admin-card p-6 space-y-4">
                <div><label class="label" for="status">Status</label><select id="status" name="status" class="field">@foreach(\App\Models\JobPosting::STATUSES as $k => $v)<option value="{{ $k }}" @selected(old('status', $job->status ?? 'draft') === $k)>{{ $v }}</option>@endforeach</select><p class="help">Only <strong>Open</strong> jobs with a publish date in the past appear on the careers page.</p></div>
                <div><label class="label" for="published_at">Publish date</label><input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $job->published_at?->format('Y-m-d\TH:i')) }}" class="field"><p class="help">Leave blank to publish immediately when set to Open.</p></div>
                <div><label class="label" for="closes_at">Closing date</label><input id="closes_at" type="date" name="closes_at" value="{{ old('closes_at', $job->closes_at?->toDateString()) }}" class="field"><p class="help">Closed automatically after this date.</p></div>
                <div class="grid grid-cols-2 gap-4"><div><label class="label" for="openings">Openings</label><input id="openings" type="number" min="1" name="openings" value="{{ old('openings', $job->openings) }}" class="field"></div><div><label class="label" for="salary_note">Pay (optional)</label><input id="salary_note" name="salary_note" value="{{ old('salary_note', $job->salary_note) }}" class="field" placeholder="e.g. TT$ 25/hr"></div></div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $job->is_featured)) class="check">Mark as urgent / featured</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="requires_licence" value="1" @checked(old('requires_licence', $job->requires_licence)) class="check">Driver's permit required</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="requires_precept" value="1" @checked(old('requires_precept', $job->requires_precept)) class="check">Precept / firearm licence required</label>
                @if($job->exists)<label class="flex items-center gap-2 text-sm text-steel-500"><input type="checkbox" name="regenerate_slug" value="1" class="check">Regenerate URL from title <span class="text-xs">(/careers/{{ $job->slug }})</span></label>@endif
            </div>
            <div class="flex flex-wrap items-center gap-2"><button class="admin-btn-primary">{{ $job->exists ? 'Save changes' : 'Create job' }}</button><a href="{{ route('admin.jobs.index') }}" class="admin-btn-secondary">Cancel</a>@if($job->exists)<span class="ml-auto"></span>@endif</div>
        </aside>
    </form>
    @if($job->exists)<div class="mt-8 flex justify-end"><x-admin-delete :action="route('admin.jobs.destroy', $job)" label="Delete job" confirm="Delete this job posting? Applications will be kept as general applications." /></div>@endif
</x-layouts.admin>
