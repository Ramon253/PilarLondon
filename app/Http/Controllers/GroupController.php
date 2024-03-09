<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Post_comment;
use Illuminate\Http\Request;
use App\Models\Student_group;
use App\Models\Group;
use App\Models\Student;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Group $group)
    {
        $student = Student::isStudent(auth()->id());
        if (isset($student)){
            Student_group::create([
                'student_id' => $student->id,
                'group_id' => $group->id
            ]);
            return redirect('/')->with(['message' => 'Joined to the class']);
        }
        return back()->withErrors(['invalid' => 'invalid request']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $student = Student::isStudent(auth()->id());
        if (!isset($student)){
            return back()->withErrors(['invalid' => 'invalid request']);
        }
        $response = [
          'group' => $group,
          'posts' => Post::query()->where('group_id' , $group->id)->get()
        ];


        return view('group.show', $response );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $student = Student::isStudent(auth()->id());
        if (isset($student)){
            Student_group::query()->where('student_id', $student->id)->where('group_id' , $group->id)->delete();
            return redirect('/')->with(['message' => 'Leaved to the class']);
        }
        return back()->withErrors(['invalid' => 'invalid request']);

    }}
