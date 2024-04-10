<?php

namespace App\Http\Middleware;

use App\Models\Student as ModelsStudent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class student
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $student = ModelsStudent::isStudent(auth()->id());
        if(!isset($student)){
            return response()->json(['error' => 'You need to be an student to access that']);
        }
        $request['student'] = $student;
        return $next($request);
    }
}
