<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Solution_file;
use App\Models\Solution_link;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $solution['student_id'] = 1;
        $solution['assignment_id'] = $assignment->id;

        $solution = Solution::create($solution);

        if ($request->has('links')) {
            $controller = new LinkController;
            $links = $controller->storeSolution($request, $solution);
        }

        if ($request->hasFile('links')) {
            $controller = new FileController;
            $files = $controller->storeSolution($request, $solution);
        }

        return response()->json(['success' => 'Response successfully stored', 'solution' => $solution]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Solution $solution)
    {
        return response()->json([
            'solution' => $solution,
            'links' => Solution_link::all()->where('solution_id', $solution->id),
            'files' => Solution_file::all()->where('solution_id', $solution->id)
        ]);
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
        Storage::deleteDirectory('solutions/'.$solution->id);
        $solution->delete();

        return response()->json(['success' => 'Response successfully deleted']);
    }
}