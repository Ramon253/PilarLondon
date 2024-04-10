<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Models\Student_group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class studentGroup

{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student = Student::isStudent(auth()->id());
        if(!isset($student)){
            return response()->json(['error' => 'You need to be an student to access that']);
        }

        $group = $request->route('group');
        $groups = Student_group::all()->where('student_id', $student->id)->where('group_id' , $group->id)->first();


        if(!isset($groups)){
            return response()->json(['error' => 'You must be part of the class you are trying to access']);
        }

        $request['student'] = $student;
        return $next($request);
    }
}
