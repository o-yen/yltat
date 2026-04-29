<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationApprovedMail;
use App\Mail\ApplicationRejectedMail;
use App\Models\Talent;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Talent::whereIn('status', ['applied', 'shortlisted'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('ic_passport_no', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('talent_code', 'like', '%' . $search . '%');
            });
        }

        $applications = $query->paginate(20)->withQueryString();

        $counts = [
            'applied'     => Talent::where('status', 'applied')->count(),
            'shortlisted' => Talent::where('status', 'shortlisted')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'counts'));
    }

    public function show(Talent $talent)
    {
        $talent->load('documents');
        return view('admin.applications.show', compact('talent'));
    }

    public function approve(Request $request, Talent $talent)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        if (! in_array($talent->status, ['applied', 'shortlisted'], true)) {
            return back()->with('error', __('messages.application_invalid_review_state'));
        }

        if (! $talent->pdpa_consent) {
            return back()->with('error', __('messages.application_approve_requires_pdpa'));
        }

        $old = $talent->status;

        $talent->update([
            'status'            => 'approved',
            'public_visibility' => true,
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
            'notes'             => $request->notes,
        ]);

        // Generate temporary password and activate the linked user account
        $temporaryPassword = strtoupper(Str::random(4)) . strtolower(Str::random(4)) . random_int(10, 99) . '!';
        $linkedUser = User::where('talent_id', $talent->id)->first();
        if ($linkedUser) {
            $linkedUser->update([
                'status' => 'active',
                'password' => $temporaryPassword,
            ]);
        }

        AuditLog::log(
            'applications',
            'approve',
            $talent->id,
            ['status' => $old],
            ['status' => 'approved']
        );

        // Send approval email with temporary password
        if ($talent->email) {
            try {
                Mail::to($talent->email)->send(new ApplicationApprovedMail($talent, $temporaryPassword));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', __('messages.application_approved'));
    }

    public function reject(Request $request, Talent $talent)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        if (! in_array($talent->status, ['applied', 'shortlisted'], true)) {
            return back()->with('error', __('messages.application_invalid_review_state'));
        }

        $old = $talent->status;

        $talent->update([
            'status'            => 'inactive',
            'public_visibility' => false,
            'rejection_reason'  => $request->rejection_reason,
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
        ]);

        AuditLog::log(
            'applications',
            'reject',
            $talent->id,
            ['status' => $old],
            ['status' => 'inactive', 'reason' => $request->rejection_reason]
        );

        // Send rejection notification email
        if ($talent->email) {
            try {
                Mail::to($talent->email)->send(new ApplicationRejectedMail($talent, $request->rejection_reason));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', __('messages.application_rejected'));
    }
}
