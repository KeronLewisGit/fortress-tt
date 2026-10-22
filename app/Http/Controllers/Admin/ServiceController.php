<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', ['services' => Service::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(['icon' => 'shield', 'is_active' => true, 'sort_order' => (Service::max('sort_order') ?? 0) + 10])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = unique_slug(Service::class, $data['name']);
        $service = Service::create($data);

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['service' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service saved.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'icon' => ['required', Rule::in(Service::ICONS)],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'features' => ['nullable', 'string', 'max:4000'],
            'ideal_for' => ['nullable', 'string', 'max:4000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);
        $data['features'] = lines_to_array($data['features'] ?? null);
        $data['ideal_for'] = lines_to_array($data['ideal_for'] ?? null);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
