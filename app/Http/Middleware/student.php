<?php

namespace App\Http\Middleware;

use App\Mail\auth;
use App\Models\parent_student;
use App\Models\Parents;
use App\Models\Student as ModelsStudent;
use App\Models\Teacher;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Pest\Plugins\Retry;
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

        if ($request->has('student_id')) {
            $id = $request->validate([
                'student_id' => ['required', Rule::exists('id', 'students')]
            ]);

            if (!$this->isTeacher() && !$this->isParent($request)) {
                return response()->json(['error' => 'you are not allowed to access that']);
            }

            $resquest['student'] = ModelsStudent::find($id['student_id']);
            return $next($request);
        }

        $user_id =  auth()->id();
        $student = ModelsStudent::where('user_id', $user_id)->firstOrFail();

        $request['student'] = $student;
        return $next($request);
    }

    /**
     * Privates
     */
    private function isTeacher(): bool
    {
        try {
            $teacher = Teacher::all()->where('user_id', auth()->id())->firstOrFail();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    private function isParent(Request $request): bool
    {
        try {
            $parent = Parents::all()->where('user_id', auth()->id())->firstOrFail();
            $parent_student = Parent_student::findOrFail([$request['student_id'], $parent->id]);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
