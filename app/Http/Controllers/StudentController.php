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
use Carbon\Carbon;
use Exception;
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

    /**
     * shows 
     */

    public function show(Request $request)
    {
        
        
        $student = $request['student'];
        $groups = $student->getGroups();
        $parents = Parents::all()->where('student_id', $student->id);
        $submissions = Solution::all()->where('student_id', $student->id);
        $user = auth()->user();

        return response()->json([
            'student' => $student,
            'user' => $user,
            'parents' => $parents,
            'groups' => $groups,
            'submissions' => $submissions
        ]);
    }

    public function dashboard(Request $request)
    {
        $student = $request['student'];
        
        $groups = $student->getGroups();
        $submissions = Solution::all()->where('student_id', $student->id);
        $posts = Post::all()->whereIn('group_id', $groups);
        $assignments = Assignment::all();
        $submittedAssginments = Assignment::all()->whereIn('id', $submissions->pluck('assignment_id'));
        $assignments = $assignments->diff($submittedAssginments);

        $posts = $posts->map(function ($item) {
            $item['links'] = Post_link::all()->where('post_id' , $item->id);
            $item['files'] = Post_file::all()->where('post_id' , $item->id);

            return $item;
        });


        return response()->json([
            'student' => $student,
            'groups' => $groups,
            'posts' => $posts,
            'submissions' => $submissions,
            'submittedAssignments' => $submittedAssginments,
            'assignments' => $assignments
        ]);
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
    public function update(Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'string',
            'surname' => 'string',
            'level' => 'string',
            'birth_date' => ['date'],
            'parent_id' => [Rule::exists('users', 'id')]
        ]);

        if ($request->hasFile('profile_photo')) {
            Storage::delete($request['student']->profile_photo);
            $formData['profile_photo'] = $this->storePhoto($request);
        }
        $request['student']->update($formData);

        return response()->json(['success' => 'Profile updated successfully']);
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

        return $file->storeAs('users/' . auth()->id());
    }
}
