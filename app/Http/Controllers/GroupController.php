<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Post;
use App\Models\Post_comment;
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
        $groups =  Group::all();
        $response = [];
        foreach($groups as $group){
            $stundentsNumber = Student_group::all()->where('group_id', $group->id)->count();
            $group['studentNumber'] = $stundentsNumber;
            $response[] = $group;
        }
        return response()->json($response);
    }

    /**
     * get all students from a group
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Group $group)
    {
        $student = Student::isStudent(auth()->id());
        if (isset($student)) {
            Student_group::create([
                'student_id' => $student->id,
                'group_id' => $group->id
            ]);
            return redirect('/group')->with(['message' => 'Joined to the class']);
        }
        return redirect('/student/create')->withErrors(['invalid' => 'You need to be a student to join the class']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $response = [
            'group' => $group,
            'posts' => Post::all()->where('group_id', $group->id),
            'assignments' => Assignment::all()->where('group_id', $group->id),
            'students' => $group->getStudents()
        ];
        return response()->json($response);
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
        if (isset($student)) {
            Student_group::query()->where('student_id', $student->id)->where('group_id', $group->id)->delete();
            return redirect('/group')->with(['message' => 'You have leaved the class successfully']);
        }
        return back()->withErrors(['invalid' => 'invalid request']);

    }
}
