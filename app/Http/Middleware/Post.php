<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Post as PostModel;
use App\Models\Student;

class Post
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = $request->route('post');

        if ($post->group_id === null) {
            return $next($request);
        }

        $user = User::find(auth()->id());
        $role = $user->getRol();

        if ($role === 'teacher')
            return $next($request);

        if ($role !== 'student') {
            return response(['error' => 'You need to be an student to access that.', 'code' => 2], 401);
        }
        $groups = Student::all()->firstWhere('user_id', $user->id)->getGroups()->pluck('id');
        if ($groups->contains($post->group_id)) {
            return $next($request);
        }
        return response(['error' => 'You need to be part of the class to access that.', 'code' => 3], 401);
    }
}
