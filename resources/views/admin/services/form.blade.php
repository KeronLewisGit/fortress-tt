<x-layouts.admin :title="$service->exists ? 'Edit service' : 'Add service'">
    <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="grid gap-6 lg:grid-cols-3">@csrf @if($service->exists)@method('PUT')@endif
        <div class="admin-card p-6 space-y-5 lg:col-span-2">
            <div class="grid gap-5 sm:grid-cols-2"><div><label class="label" for="name">Name *</label><input id="name" name="name" value="{{ old('name', $service->name) }}" required maxlength="120" class="field"></div><div><label class="label" for="eyebrow">Category label</label><input id="eyebrow" name="eyebrow" value="{{ old('eyebrow', $service->eyebrow) }}" maxlength="80" class="field" placeholder="e.g. Manned guarding"></div></div>
            <div><label class="label" for="summary">Summary *</label><textarea id="summary" name="summary" rows="2" maxlength="500" required class="field">{{ old('summary', $service->summary) }}</textarea></div>
            <div><label class="label" for="description">Description</label><textarea id="description" name="description" rows="7" class="field" placeholder="Separate paragraphs with a blank line.">{{ old('description', $service->description) }}</textarea></div>
            <div class="grid gap-5 sm:grid-cols-2"><div><label class="label" for="features">What's included (one per line)</label><textarea id="features" name="features" rows="7" class="field text-sm">{{ old('features', implode("\n", $service->features ?? [])) }}</textarea></div><div><label class="label" for="ideal_for">Ideal for (one per line)</label><textarea id="ideal_for" name="ideal_for" rows="7" class="field text-sm">{{ old('ideal_for', implode("\n", $service->ideal_for ?? [])) }}</textarea></div></div>
        </div>
        <aside class="space-y-6"><div class="admin-card p-6 space-y-4">
            <div><label class="label">Icon</label><div class="grid grid-cols-5 gap-2">@foreach(\App\Models\Service::ICONS as $i)<label class="flex cursor-pointer items-center justify-center rounded-lg border p-2.5 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50" title="{{ $i }}"><input type="radio" name="icon" value="{{ $i }}" class="sr-only" aria-label="{{ $i }}" @checked(old('icon', $service->icon) === $i)><x-icon :name="$i" class="h-5 w-5 text-navy-900" /></label>@endforeach</div></div>
            <div><label class="label" for="sort_order">Sort order</label><input id="sort_order" type="number" min="0" max="1000" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}" required class="field"></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $service->is_featured)) class="check">Featured on home page (first 4 shown)</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active)) class="check">Visible on website</label>
        </div>
        <div class="flex gap-2"><button class="admin-btn-primary">Save</button><a href="{{ route('admin.services.index') }}" class="admin-btn-secondary">Cancel</a></div></aside>
    </form>
    @if($service->exists)<div class="mt-6 flex justify-end"><x-admin-delete :action="route('admin.services.destroy', $service)" label="Delete service" confirm="Delete this service? It will disappear from the website immediately." /></div>@endif
</x-layouts.admin>
