<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class BaseCompanyController extends Controller
{
    /**
     * Resolve the authenticated company rep's company.
     * Tries company_id FK first, then falls back to contact_email match.
     */
    protected function getCompany(): Company
    {
        $user = Auth::user();

        // Primary: explicit FK on users table
        $company = $user->company;

        // Fallback: match by contact email (for seeded/legacy accounts)
        if (! $company) {
            $company = Company::where('contact_email', $user->email)->first();
        }

        if (! $company) {
            abort(403, __('messages.company_record_not_found'));
        }

        return $company;
    }
}
