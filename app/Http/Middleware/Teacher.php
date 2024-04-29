<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Teacher as ModelTeacher;

class Teacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $teacher = ModelTeacher::findOrFail(auth()->id());
            $request['teacher'] = $teacher;
            
        }catch(ModelNotFoundException $e){
            return response()->json(['error' => 'You must be a techaer to access this']);
        }
        return $next($request);
    }
}
