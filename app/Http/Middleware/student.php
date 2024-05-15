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
use Illuminate\Support\ItemNotFoundException;
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
        $request['student'] = false;
        $request['teacher'] = false;

        if ($request->has('student_id')) {
            $id = $request->validate([
                'student_id' => ['required', Rule::exists('id', 'students')]
            ]);

            if (!$this->isTeacher() && !$this->isParent($request)['isParent']) {
                return response()->json(['error' => 'you are not allowed to access that']);
            }

            $resquest['student'] = ModelsStudent::find($id['student_id']);
            return $next($request);
        }

        $user_id =  auth()->id();
        try {
            $student = ModelsStudent::where('user_id', $user_id)->firstOrFail();
        }catch (ModelNotFoundException $exception){
            $teacher = $this->isTeacher();
            if ($teacher['isTeacher']){
                $request['teacher'] = $teacher['teacher'];
                return $next($request);
            }
            return response()->json([
                'error' => 'You need to be an student to access that route'
            ], 404);
        }

        $request['student'] = $student;
        return $next($request);
    }

    /**
     * Privates
     */
    private function isTeacher(): array
    {
        try {
            $teacher = Teacher::all()->where('user_id', auth()->id())->firstOrFail();
            return ['isTeacher' => true, 'teacher' => $teacher];
        } catch (ModelNotFoundException $e) {
            return ['isTeacher' => false];
        }
    }

    private function isParent(Request $request): array
    {
        try {
            $parent = Parents::all()->where('user_id', auth()->id())->firstOrFail();
            $parent_student = Parent_student::findOrFail([$request['student_id'], $parent->id]);
            return ['isParent' => true,'parent' => $parent_student];
        } catch (ModelNotFoundException $e) {
            return ['isParent' => false];
        }
    }
}
