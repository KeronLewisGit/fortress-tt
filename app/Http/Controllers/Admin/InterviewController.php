<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InterviewSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterviewController extends Controller
{
    public function index()
    {
        return view('admin.interviews.index', [
            'slots' => InterviewSlot::upcoming()->withCount('bookings')->with('bookings.job')->get(),
            'scheduled' => Application::with(['job', 'assignee'])->whereNotNull('interview_at')->where('interview_at', '>=', now()->startOfDay())->orderBy('interview_at')->get(),
            'awaiting' => Application::with('job')->where('status', 'interview')->whereNull('interview_at')->latest('status_changed_at')->get(),
            'pastCount' => InterviewSlot::where('starts_at', '<', now()->startOfDay())->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:240'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'repeat' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);
        $start = Carbon::parse($data['date'].' '.$data['time']);
        for ($i = 0; $i < $data['repeat']; $i++) {
            InterviewSlot::create([
                'starts_at' => $start->copy()->addMinutes($i * $data['duration_minutes']),
                'duration_minutes' => $data['duration_minutes'],
                'location' => $data['location'],
                'capacity' => $data['capacity'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);
        }

        return back()->with('status', $data['repeat'].' interview '.Str::plural('slot', $data['repeat']).' added. Applicants at the interview stage can now book them.');
    }

    public function destroy(InterviewSlot $slot)
    {
        if ($slot->bookings()->exists()) {
            return back()->withErrors(['slot' => 'That slot has bookings – clear the applicants\' interview dates first.']);
        }
        $slot->delete();

        return back()->with('status', 'Slot removed.');
    }
}
