<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Models\Student_group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isNull;

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
        if (!isset($student)) {
            return response()->json(['error' => 'You need to be an student to access that']);
        }

        $group = (null !== $request->route('group')) ? $request->route('group')->id : $request->route('post')->group_id;

        if($group === null)
            return $next($request);
        
        
        $groups = Student_group::all()->where('student_id', $student->id)->where('group_id', $group)->first();


        if (!isset($groups)) {
            return response()->json(['error' => 'You must be part of the class you are trying to access']);
        }

        $request['student'] = $student;
        return $next($request);
    }
}
