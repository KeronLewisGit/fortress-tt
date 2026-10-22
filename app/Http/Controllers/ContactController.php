<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryAlert;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return back()->with('status', 'Thank you – we will be in touch shortly.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $enquiry = Enquiry::create($data + ['ip' => $request->ip()]);

        try {
            if ($to = notification_addresses('notifications.enquiry_email')) {
                Mail::to($to)->send(new EnquiryAlert($enquiry));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('contact')->with('status', 'Thank you, '.$enquiry->name.'. Your request has been received – a Fortress representative will contact you within one business day.');
    }
}
