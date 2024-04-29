<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Models\Student_group;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Teacher;

class studentGroup

{

    public function handle(Request $request, Closure $next): Response
    {
        if (Teacher::all()->where('user_id', auth()->id())->first()->isNotEmpty()) {
            return $next($request);
        }
        try {
            $group = $request->route('group') ?? Group::findOrFail(
                $request->group_id ??
                    $request->route('post')->group_id ??
                    $request->route('assignment')->group_id
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'group not found'], 404);
        }

        $student = $request['student'];

        try {
            Student_group::all()->where('student_id', $student->id)->where('group_id', $group)->firstOrFail();
            return $next($request);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'you do not belong to this group'], 404);
        }
    }
}
