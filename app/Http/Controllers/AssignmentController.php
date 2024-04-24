<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Assignment;
use App\Http\Controllers\Controller;
use App\Models\Assignment_comment;
use App\Models\Assignment_file;
use App\Models\Assignment_link;
use App\Models\Group;
use App\Models\Post_file;
use App\Models\Student_group;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{

    /**Getters  */
    public function index(Request $request)
    {
        /*        $groups = Student_group::all()->where('student_id', $request['student']->id)->pluck('group_id');
        $assignments = Assignment::all()->whereIn('group_id', $groups);*/
        $assignments = Assignment::all();
        return response()->json($assignments);
    }

    public function show(Assignment $assignment)
    {
        $assignment['files'] = Assignment_file::all()->where('assignment_id', $assignment->id);
        $assignment['links'] = Assignment_link::all()->where('assignment_id', $assignment->id);
        $assignment['comments'] = Assignment_comment::all()->where('assignment_id', $assignment->id);

        return response()->json($assignment);
    }


    /**
     * Store functions
     */

    public function store(Request $request)
    {
        $assignment = $request->validate([
            'name' => ['required', 'string'],
            'dead_line' => ['required', 'date'],
            'description' => ['string'],
            'group_id' => ['required', Rule::exists('groups', 'id')]
        ]);

        $assignment = Assignment::create($assignment);

        if ($request->has('links')) {
            $controller = new LinkController;
            $result = $controller->storeAssignment($request, $assignment);
        }

        if ($request->hasFile('files')) {
            $controller = new FileController;
            $result = $controller->storeAssignment($request, $assignment);
        }

        return response()->json(['message' => 'assignment created successfully']);
    }


    /**
     * Destroy functions.
     */
    public function destroy(Assignment $assignment)
    {
        Storage::deleteDirectory("assignments/".$assignment->id);
        $assignment->delete();
        return response()->json(['message' => 'assignment deleted successfully']);
    }


    /**
     * Update
     */
    public function update(Request $request, Assignment $assignment)
    {
        $assignmentData = $request->validate([
            'name' => ['string'],
            'dead_line' => ['string'],
            'description' => ['string'],
        ]);

        $assignment->update($assignmentData);
        return response()->json(['success' => 'post successfully updated']);
    }



}