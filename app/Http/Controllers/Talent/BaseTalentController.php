<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class BaseTalentController extends Controller
{
    protected function getTalent(): Talent
    {
        $user = Auth::user();

        $talent = $user->talent;

        if (! $talent) {
            $talent = Talent::whereHas('linkedUser', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->first();
        }

        if (! $talent && $user?->email) {
            $talent = Talent::where('email', $user->email)->first();
        }

        if (! $talent) {
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            throw new HttpResponseException(
                redirect()
                    ->route('login')
                    ->withErrors(['email' => __('messages.talent_record_not_found')])
            );
        }

        return $talent;
    }
}
