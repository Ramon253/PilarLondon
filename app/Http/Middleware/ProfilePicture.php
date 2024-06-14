<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use App\Models\Post_comment;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfilePicture
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $public_comment = Post_comment::all()->where('user_id', auth()->id())->where('public', true)->first();
        if ($public_comment)
            return $next($request);

        $user = User::find(auth()->id());
        $target = $request->route('user');

        if ($user->id === $target->id)
            return $next($request);

        $role = $user->getRol();

        if ($role !== 'none')
            return $next($request);

        return \response()->json(['message' => 'Unauthorized'], 401);
    }
}
