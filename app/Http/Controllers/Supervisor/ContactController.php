<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;

class ContactController extends Controller
{
    public function index()
    {
        return view('supervisor.contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'issue' => 'required|string|max:5000',
        ]);

        try {
            // Send email to fixed admin email
            Mail::to('programmerqa@gmail.com')->send(new ContactUsMail(
                $request->name,
                $request->phone_number,
                $request->issue,
                auth()->user()->name . ' (Supervisor)'
            ));

            return back()->with('success', 'Your message has been sent successfully. We will get back to you soon.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }
}
