<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Post;
use App\Models\Post_comment;
use App\Models\Student_group;
use App\Models\Group;
use App\Models\Student;
use App\Models\Post_file;
use App\Models\Post_link;
use App\Models\Assignment_file;
use App\Models\Assignment_link;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\MockObject\Builder\Stub;

class GroupController extends Controller
{
    /**
     * Shows.
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

        $posts = $this->getPosts($group);
        $assignments = $this->getAssignments($group);

        $response = [
            'group' => $group,
            'posts' => $posts,
            'assignments' => $assignments,
            'students' => $group->getStudents()
        ];
        return response()->json($response);
    }

    public function showPosts(Group $group)
    {
        return response()->json($this->getPosts($group));
    }

    public function showAssignments(Group $group)
    {
        return response()->json($this->getAssignments($group));
    }


    /**
     * Store
     */

    public function store(Request $request)
    {
        $group = $request->validate([
            'level' => ['required', 'in:A1,A2,B1,B2,C1,C2'],
            'capacity' => ['required', 'integer'],
            'lesson_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'lesson_days' => ['required', 'in:l-m,m-j,v']
        ]);

        $group['teacher_id'] = $request['teacher']->id;

        if ($request->hasFile('banner')) {
            $group['banner'] = $request->file('banner')->store('groups/');
        }

        $group = Group::create($group);

        return response()->json([
            'success' => 'group successfully created',
            'group' => $group
        ]);
    }

    /**
     * Updates
     */
    public function update(Request $request, Group $group)
    {
        $groupData = $request->validate([
            'level' => ['string', 'in:A1,A2,B1,B2,C1,C2'],
            'capacity' => ['integer'],
            'lesson_time' => ['regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'lesson_days' => ['string', 'in:l-m,m-j,v']
        ]);

        if ($request->hasFile('banner')) {
            Storage::delete($group->banner);
            $groupData['banner'] = $request->file('banner')->store('groups/');
        }

        $group->update($groupData);

        return response()->json([
            'success' => 'group successfully updated',
            'group' => $group
        ]);
    }

    /**
     * Remove .
     */
    public function destroy(Group $group)
    {
        if ($group->banner !== null) {
            Storage::delete($group->banner);
        }
        $group->delete();

        return response()->json([
            'success' => 'group successfully destroyed'
        ]);
    }


    /**
     * Manage students
     */
    public function join(Request $request, Group $group)
    {

        $studentId = $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')]
        ]);

        $student_id = $studentId['student_id'];


        Student_group::create([
            'student_id' => $student_id,
            'group_id' => $group->id
        ]);

        return response()->json(['sucess' => 'the student has joined the class successfully']);
    }

    
    public function kick(Request $request, Group $group, Student $student)
    {

        $studentId = $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')]
        ]);

        $student_id = $studentId['student_id'];


        Student_group::destroy([
            'student_id' => $student_id,
            'group_id' => $group->id
        ]);

        return response()->json(['sucess' => 'the student has leaved the class successfully']);
    }




    /*
    * Privates 
    */

    private function getPosts(Group $group): Collection
    {

        $posts = Post::all()->where('group_id', $group->id);

        foreach ($posts as $id => $post) {
            $posts[$id]['links'] = Post_link::all()->where('post_id', $post->id);
            $posts[$id]['files'] = Post_file::all()->where('post_id', $post->id);
        }

        return $posts;
    }
    private function getAssignments(Group $group): Collection
    {

        $assignments =  Assignment::all()->where('group_id', $group->id);


        foreach ($assignments as $id => $assignment) {
            $assignments[$id]['links'] = Assignment_link::all()->where('assignment_id', $assignment->id);
            $assignments[$id]['files'] = Assignment_file::all()->where('assignment_id', $assignment->id);
        }
        return $assignments;
    }
}
