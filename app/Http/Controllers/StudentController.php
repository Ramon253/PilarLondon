<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Models\Student_group;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    public function edit()
    {
        $student = Student::isStudent(auth()->id());
        if (!isset($student)) {
            return redirect('/student/create')->withErrors(['invalid' => 'you must be an student to edit one']);
        }
        return view('students.edit', ['student' => $student]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'required',
            'surname' => 'required',
            'level' => 'required',
            'birth_date' => ['required', 'date'],
        ]);
        $formData['user_id'] = auth()->id();
        $formData['birth_date'] = Carbon::parse($formData['birth_date']);
        Student::create($formData);

        return redirect('/')->with('message', 'Welcome to pilarLondon');
    }
    public function update(Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'required',
            'surname' => 'required',
            'level' => 'required',
            'birth_date' => 'required',
        ]);
        $student = Student::query()->where('user_id' , \auth()->id())->first();
        $student->update($formData);
        return redirect('/student')->with('message', 'Welcome to pilarLondon');
    }

    public function show()

    {
        $student = Student::isStudent(auth()->id());
        if (!isset($student))
            return redirect('/');
        return view('students.show', [
            'student' => $student
        ]);
    }

}
