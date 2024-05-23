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
use App\Models\Wait_list;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\MockObject\Builder\Stub;
use function PHPUnit\Framework\isEmpty;

class GroupController extends Controller
{
    /**
     * Shows.
     */
    public function index()
    {
        $user = User::find(auth()->id());
        $role = $user->getRol();
        if ($role === 'teacher') {
            return response()->json(array_values(Group::all()->toArray()));
        }
        if ($role === 'student') {
            return response()->json(array_values(Student::all()->where('user_id', $user->id)->firstOrFail()->getGroups()->toArray()));
        }
        $groups = Group::all()->map(function (Group $group) {
            $group['studentNumber'] = Student_group::all()->where('group_id', $group->id)->count();
            return $group;
        });

        return response()->json($groups);
    }


    public function show(Group $group, Request $request)
    {
        $group['posts'] = $this->getPosts($group);
        $group['assignments'] = $this->getAssignments($group);

        if ($request['teacher']) {
            $students = $group->getStudents()->map(function ($student) {
                $student['age'] = Carbon::parse($student['birth_date'])->age;
                return $student;
            });
            $group['students'] = $students;
        }
        return response()->json($group);
    }

    public function showPosts(Group $group)
    {
        return response()->json($this->getPosts($group));
    }

    public function showAssignments(Group $group)
    {
        return response()->json($this->getAssignments($group));
    }

    public function showBanner(Group $group)
    {
        if (Storage::has($group->banner)) {
            return Storage::get($group->banner);
        }
        return file_get_contents(public_path('assets/defaultBanner.png'));
    }


    /**
     * Store
     */

    public function store(Request $request)
    {
        $group = $request->validate([
            'name' => ['required'],
            'level' => ['required', 'in:A1,A2,B1,B2,C1,C2'],
            'capacity' => ['required', 'integer'],
            'lessons_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'lesson_days' => ['required', 'in:l-m,m-j,v'],
            'banner' => ['required', 'file'],
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

    /*
     * Wait list
     * */
    public function joinWaitList(Request $request, Group $group)
    {
        $formData = $request->validate([
            'places' => ['required', 'integer', 'unsigned'],
            'phone_number' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
        ]);
        $formData['user_id'] = auth()->id();
        $formData['group_id'] = $group->id;
        $result = Wait_list::create($formData);

        if ($result)
            return response()->json([
                'success' => $result
            ]);
        return response()->json(['error' => 'falied to join waitlist', 500]);
    }

    public function leaveWaitList(Group $group)
    {
        $wait = Wait_list::all()->where('user_id', auth()->id())->where('group_id', $group->id)->first();
        $wait->delete();
        return response()->json([
            'success' => $wait
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
        $student_id = $request->validate(
            ['student_id' => ['required', Rule::exists('students', 'id')]]
        );

        Student_group::create([
            'student_id' => $student_id['student_id'],
            'group_id' => $group->id
        ]);

        return response()->json(['success' => 'the student has joined the class successfully']);
    }


    public function kick(Request $request, Group $group)
    {
        $student_id = $request->validate(
            ['student_id' => ['required', Rule::exists('students', 'id')]]
        );

        $relation = Student_group::where('student_id', $student_id['student_id'])->where('group_id', $group->id)->delete();

        return response()->json(['sucess' => 'the student has leaved the class successfully']);
    }


    /*
    * Privates
    */

    private function getPosts(Group $group)
    {
        return array_values(Post::all()->where('group_id', $group->id)->map(function (Post $post) {
            $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
            $post['files'] = array_values(Post_file::all()
                ->where('post_id', $post->id)
                ->map(fn($file) => collect($file)->except('file_path'))->toArray());

            return $post;
        })->toArray());
    }

    private function getAssignments(Group $group)
    {

        return array_values(Assignment::all()->where('group_id', $group->id)->map(function (Assignment $assignment) {
            $assignment['links'] = array_values(Assignment_link::all()->where('assignment_id', $assignment->id)->toArray());
            $assignment['files'] = array_values(Assignment_file::all()
                ->where('assignment_id', $assignment->id)
                ->map(fn($file) => collect($file)->except('file_path'))->toArray());

            return $assignment;
        })->toArray());

    }
}
