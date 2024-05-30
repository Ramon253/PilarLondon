<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Assignment_file;
use App\Models\Assignment_link;
use App\Models\Group;
use App\Models\Join_code;
use App\Models\Solution;
use App\Models\Student_group;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Teacher::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $join_code = Join_code::all()->where('user_id', auth()->id())->firstOrFail();
            if ($join_code->role !== 'teacher') {
                return response()->json(['error' => 'You cant be student, your role is' . $join_code->role]);
            }
        } catch (ItemNotFoundException $e) {
            return response()->json(['error' => 'You are not allowed to yet to join the class'], 400);
        }
    }

    public function dashboard(Request $request)
    {
        $solutions = Solution::all()->whereNull('note');
        $assignments = array_values(Assignment::all()->whereIn('id', $solutions->unique('assignment_id')->pluck('assignment_id'))->map(
            function ($assignment) use($solutions){
                $group = Group::find($assignment->group_id);
                $assignment['file_links'] = array_values(Assignment_file::all()->where('assignment_id', $assignment->id)->toArray());
                $assignment['links'] = array_values(Assignment_link::all()->where('assignment_id', $assignment->id)->toArray());
                $assignment['submitted'] = Solution::all()->where('assignment_id', $assignment->id)->count();
                $assignment['marked'] = $assignment['submitted'] - $solutions->where('assignment_id', $assignment->id)->count();
                $assignment['members'] = Student_group::all()->where('group_id', $group->id)->count();
                $assignment['group_name'] = $group->name;
                return $assignment;
            }
        )->toArray());
        $marked = 100 - ($solutions->count() * 100 / Solution::all()->count());
        return response()->json([
            'teacher' => $request['teacher'],
            'assignments' => $assignments,
            'marked' => $marked
        ]);
    }

    public static function generateStudent(Request $request)
    {
        $code = '';
        do {
            $code = Str::random(50);
            $model = Join_code::find($code);
        } while ($model !== null);

        $join_code = Join_code::create([
            'code' => $code,
            'role' => 'student'
        ]);
        return $code;
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}
