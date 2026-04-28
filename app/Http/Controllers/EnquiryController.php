<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'address'              => 'required|string|max:1000',
            'phone'                => 'required|string|max:20',
            'email'                => 'nullable|email|max:255',
            'occupation'           => 'required|string|max:255',
            'affiliate_interest'   => 'required|in:yes,no',
            'affiliate_experience' => 'nullable|string|max:2000',
        ]);

        $enquiry = Enquiry::create($validated);

        try {
            Mail::send([], [], function ($message) use ($enquiry) {
                $message->to(env('ADMIN_EMAIL'))
                    ->subject('New Enquiry from ' . $enquiry->name)
                    ->html(
                        '<h2 style="color:#0A2342">New Enquiry — Yesb Confident</h2>
                        <table style="border-collapse:collapse;width:100%;font-family:sans-serif">
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Name</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->name) . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Phone</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->phone) . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Email</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->email ?? '—') . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Address</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->address) . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Occupation</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->occupation) . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Affiliate Interest</b></td><td style="padding:8px;border:1px solid #ddd">' . strtoupper(e($enquiry->affiliate_interest)) . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Experience</b></td><td style="padding:8px;border:1px solid #ddd">' . e($enquiry->affiliate_experience ?? '—') . '</td></tr>
                            <tr><td style="padding:8px;border:1px solid #ddd;background:#f9f9f9"><b>Submitted</b></td><td style="padding:8px;border:1px solid #ddd">' . $enquiry->created_at->format('d/m/Y h:i A') . '</td></tr>
                        </table>
                        <p style="margin-top:20px">
                            <a href="' . url('/admin/dashboard') . '" style="background:#0A2342;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">View in Admin Panel</a>
                        </p>'
                    );
            });
        } catch (\Exception $e) {
            // Silent fail — don't break form if mail fails
        }

        return back()->with('success', 'Enquiry submitted successfully!');
    }
}
