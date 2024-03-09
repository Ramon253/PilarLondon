<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Models\Student_group;
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
        return view('students.edit');
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
            'birth_date' => 'required',
        ]);
        $formData['user_id'] = auth()->user();

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
        $formData['user_id'] = auth()->user();

        Student::update($formData);

        return redirect('/')->with('message', 'Welcome to pilarLondon');
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
