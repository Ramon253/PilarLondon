<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Join_code;
use App\Models\Parents;
use App\Models\Post;
use App\Models\Post_file;
use App\Models\Post_link;
use App\Models\Solution;
use App\Models\Student_group;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use http\Env\Response;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Return_;

class StudentController extends Controller
{

    public function index()
    {
        return response()->json(array_values(Student::all()->map(
            function ($student) {
                $student['age'] = Carbon::parse($student->birth_date)->age;
                return $student;
            }
        )->toArray()));
    }

    /**
     * shows
     */

    public function show(Request $request, Student $student)
    {
        return $this->getProfile($student, User::find($student->user_id));
    }

    public function profile(Request $request)
    {
        $student = $request['student'];
        return $this->getProfile($student, auth()->user());
    }

    public function dashboard(Request $request)
    {
        $student = $request['student'];

        $groups = $student->getGroups();
        $assignments = Assignment::all()->whereIn('group_id', $groups->pluck('id'));
        $todoAssignments = $assignments->filter(function ($assignment) use ($student) {
            return !Solution::all()
                ->where('assignment_id', $assignment->id)
                ->where('student_id', $student->id)->first();
        })->sortBy('dead_line');

        $solutions = Solution::all()->where('student_id', $student->id)->sortBy('updated_at')->map(
            function ($solution) {
                $assignment = Assignment::find($solution->assignment_id);
                $solution['assignment_name'] = $assignment->name;
                $solution['group_name'] = Group::all()->where('id', $assignment->group_id)->first()->name;
                return $solution;
            }
        )->toArray();
        $assignmentsNum = $assignments->count();
        if ($todoAssignments->count() === 0) $answered = 0;
        else
            $answered = ($assignmentsNum === 0) ? 0 : ($todoAssignments->count() * 100) / $assignmentsNum;


        return response()->json([
            'student' => $student,
            'groups' => $groups,
            'assignments' => array_values($todoAssignments->toArray()),
            'answered' => $answered,
            'solutions' => array_values($solutions)
        ]);
    }

    public function postsDashboard(Request $request)
    {
        if ($request['teacher']) {
            return response()->json([
                'posts' => array_values(Post::all()->where('group_id', '<>', null)->map(function ($post){
                    $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
                    $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());
                    return $post;
                })->toArray()),
                'groups' => array_values(Group::all()->map(function ($group) {
                    $result['id'] = $group['id'];
                    $result['name'] = $group['name'];
                    return $result;
                })->toArray())
            ]);
        }
        $student = $request['student'];
        $groups = $student->getGroups();
        $posts = Post::all()->whereIn('group_id', $groups->map(fn($group) => $group['id']))->map(function ($post) {
            $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
            $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());
            return $post;
        });
        return response()->json([
            'groups' => $groups,
            'posts' => array_values($posts->toArray())
        ]);
    }

    public function assignmentsDashboard()
    {

    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        try {
            $join_code = Join_code::all()->where('user_id', auth()->id())->firstOrFail();
            if ($join_code->role !== 'student') {
                return response()->json(['error' => 'You cant be student, your role is' . $join_code->role]);
            }
        } catch (ItemNotFoundException $e) {
            return response()->json(['error' => 'You are not allowed to yet to join the class'], 400);
        }
        $formData = $request->validate([
            'full_name' => 'required',
            'surname' => 'required',
            'level' => 'required',
            'birth_date' => ['required', 'date'],
            'parent_id' => [Rule::exists('users', 'id')]
        ]);

        $formData['user_id'] = auth()->id();
        $formData['birth_date'] = Carbon::parse($formData['birth_date']);

        if ($request->hasFile('profile_photo')) {
            $formData['profile_photo'] = $this->storePhoto($request);
        }

        $student = Student::create($formData);
        $join_code->delete();

        return response()->json(['success' => 'Welcome to pilar london', 'student' => $student]);
    }


    /**
     * Updates
     */
    public function putProfileImage(Request $request)
    {
        $formData = $request->validate([
            'profile_photo' => ['file', 'required']
        ]);
        if ($request['student']->profile_photo !== null)
            Storage::delete($request['student']->profile_photo);

        $path = $this->storePhoto($request);
        $request['student']->profile_photo = $path;
        $request['student']->save();

        return \response()->json(['success' => 'Uploaded successfully', 'path' => $path]);
    }

    public function update(Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'string',
            'surname' => 'string',
            'level' => 'string',
            'birth_date' => ['date'],
        ]);
        $request['student']->update($formData);
        $request['student']['age'] = Carbon::parse($request['student']->birth_date)->age;
        return response()->json(['success' => 'Profile updated successfully', 'student' => $request['student']]);
    }


    /**
     * Destroy
     */

    public function destroy(Request $request, Student $student)
    {
        Storage::deleteDirectory('users/' . auth()->id());
        $student->delete();
        return response()->json(['success' => 'Student deleted successfully']);
    }

    /**
     * Privates
     */

    private function storePhoto(Request $request)
    {
        $file = $request->file('profile_photo');

        $mimeType = $file->getClientMimeType();

        if (!Str::contains($mimeType, 'image')) {
            return response()->json(['error' => 'Invalid file, please send an image']);
        }

        return $file->store('users/' . auth()->id());
    }

    private function getProfile($student, $user)
    {
        $groups = array_values($student->getGroups()->toArray());
        $parents = array_values(Parents::all()->where('student_id', $student->id)->map(
            function ($parent) {
                $parent['user'] = User::find($parent->id);
                return $parent;
            }
        )->toArray());

        $submissions = array_values(Solution::all()->where('student_id', $student->id)->map(function ($solution) {
            $assignment = Assignment::find($solution->assignment_id);
            $group = Group::find($assignment->group_id);

            $solution['assignment_id'] = $assignment->id;
            $solution['assignment_name'] = $assignment->name;
            $solution['group_name'] = $group->name;
            $solution['group_id'] = $assignment->group_id;
            return $solution;
        })->toArray());

        $student['age'] = Carbon::parse($student->birth_date)->age;

        return response()->json([
            'student' => $student,
            'user' => $user,
            'parents' => $parents,
            'groups' => $groups,
            'submissions' => $submissions
        ]);
    }
}
