<?php

namespace App\Http\Controllers;

use App\Mail\ContactReceived;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'contact' => 'required|string|max:190', 'subject' => 'required|in:bike,service,sell,other', 'message' => 'required|string|max:4000', 'consent' => 'accepted', 'website' => 'nullable|max:0']);
        $entry = ContactRequest::create([...$data, 'consent' => true, 'ip_hash' => hash('sha256', (string) $request->ip().config('app.key'))]);
        Mail::to(config('contact.recipient'))->queue(new ContactReceived($entry));

        return back()->with('status', 'Takk! Henvendelsen er sendt.');
    }
}
