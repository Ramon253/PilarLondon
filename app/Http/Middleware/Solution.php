<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Solution
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(auth()->id());
        $role = $user->getRol();

        if ($role === 'teacher') {
            return $next($request);
        }
        if ($role !== 'student') {
            return response('Unauthorized.', 401);
        }
        $url = $request->url();

        $solution = null;
        if (Str::contains($url, 'file')) {
            $solution = $request->route('solution_file');
        }
        if (Str::contains($url, 'link')) {
            $solution = $request->route('solution_link');
        }
        if ($solution !== null) {
            $solution = Solution::findOrFail($solution->solution_id);
        } else {
            $solution = $request->route('solution');
        }

        if ($solution->user_id !== auth()->id()) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}
