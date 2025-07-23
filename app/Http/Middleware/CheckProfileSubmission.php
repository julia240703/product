<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfileSubmission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Check if the profile exists and if the national_id is null
        if ($profile && $profile->national_id === null) {
            // Redirect to input.profile.data if the national_id is null
            return redirect()->route('input.profile.data');
        }

        return $next($request);
    }
}
