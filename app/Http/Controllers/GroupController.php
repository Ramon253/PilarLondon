<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Post;
use App\Models\Post_comment;
use App\Models\Student_group;
use App\Models\Group;
use App\Models\Student;
use App\Models\Post_file;
use App\Models\Post_links;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Redis;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups =  Group::all();
        $response = [];
        foreach ($groups as $group) {
            $stundentsNumber = Student_group::all()->where('group_id', $group->id)->count();
            $group['studentNumber'] = $stundentsNumber;
            $response[] = $group;
        }
        return response()->json($response);
    }


    public function show(Group $group)
    {
        $posts = Post::all()->where('group_id', $group->id);

        foreach ($posts as $id => $post) {
            $posts[$id]['links'] = Post_links::all()->where('post_id', $post->id);
            $posts[$id]['files'] = Post_file::all()->where('post_id', $post->id);
        }

        $response = [
            'group' => $group,
            'posts' => $post,
            'assignments' => Assignment::all()->where('group_id', $group->id),
            'students' => $group->getStudents()
        ];
        return response()->json($response);
    }


    public function getPosts(string $group)
    {
        $posts = Post::all()->where('group_id', $group);
        return response()->json([
            'success' => 'success',
            'posts' => $posts
        ]);
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
