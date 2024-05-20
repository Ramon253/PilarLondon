<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Solution;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Solution_file;
use App\Models\Solution_link;
use App\Rules\gradeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Assignment $assignment)
    {
        $responses = Solution::all()->where('assignment_id', $assignment->id);
        if (!isset($responses)) {
            return response()->json(['error' => 'no responses to that assignment']);
        }
        return response()->json($responses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Assignment $assignment)
    {
        $solution = $request->validate([
            'description' => ['string']
        ]);
        $solution['student_id'] = $request['student']->id;
        $solution['assignment_id'] = $assignment->id;

        $solution = Solution::create($solution);

        if ($request->has('links')) {
            $controller = new LinkController;
            $solution['links'] = array_values($controller->store($request, 'solution', $solution->id  , new Solution_link)['links']);
        }

        if ($request->hasFile('files')) {
            $controller = new FileController;
            $solution['fileLinks'] = array_values($controller->store($request, 'solution', $solution->id, new Solution_file)['files']);
        }

        return response()->json(['success' => 'Response successfully stored', 'solution' => $solution]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Solution $solution)
    {
        $student = Student::find($solution->student_id);
        $solution['links'] = array_values(Solution_link::all()->where('solution_id', $solution->id)->toArray());
        $solution['fileLinks'] = array_values(Solution_file::all()->where('solution_id', $solution->id)->toArray());
        $solution['student_name'] = $student->full_name;
        $solution['user_id'] = $student->user_id;
        $solution['student_id'] = $student->id;
        return response()->json($solution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solution $solution)
    {
        $newSolution = $request->validate(['description' => ['string']]);

        $result = $solution->update($newSolution);

        return ((int)$result === 1) ? response()->json(['success' => 'Response successfully updated', 'solution' => $solution]) : response()->json(['error' => 'Error updating ']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solution $solution)
    {
        Storage::deleteDirectory('solutions/' . $solution->id);
        $solution->delete();

        return response()->json(['success' => 'Response successfully deleted']);
    }


    /**
     * Grade function
     */

    public function grade(Request $request, Solution $solution)
    {
        $grade = $request->validate([
            'note' => ['required', 'numeric', 'decimal:0,2', 'min:1', 'max:10'],
        ]);
        $result = $solution->update($grade);


        return ((int)$result === 1) ? response()->json([
            'success' => 'Solution graded successfully'
        ]) :
            response()->json([
                'error' => 'Error grading the response'
            ]);

    }
}
