<?php

namespace App\Http\Middleware;

use App\Models\Student as ModelsStudent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Student
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id =  auth()->id();
        $student = ModelsStudent::where('user_id',$user_id )->first();

        if (isset($student) || ModelsStudent::isParent($user_id)) {
            $request['student'] = $student;
            return $next($request);
        }
        
        return response()->json(['error' => 'You need to be an student to access that']);
    }
}
