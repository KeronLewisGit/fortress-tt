<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    public function index()
    {
        return view('admin.templates.index', ['templates' => MessageTemplate::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.templates.form', ['template' => new MessageTemplate(['sort_order' => (MessageTemplate::max('sort_order') ?? 0) + 1])]);
    }

    public function store(Request $request)
    {
        $template = MessageTemplate::create($this->validated($request));

        return redirect()->route('admin.templates.edit', $template)->with('status', 'Template created.');
    }

    public function edit(MessageTemplate $template)
    {
        return view('admin.templates.form', ['template' => $template]);
    }

    public function update(Request $request, MessageTemplate $template)
    {
        $template->update($this->validated($request));

        return redirect()->route('admin.templates.edit', $template)->with('status', 'Template saved.');
    }

    public function destroy(MessageTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')->with('status', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:8000'],
            'set_status' => ['nullable', Rule::in(array_keys(Application::STAGES))],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);
    }
}
