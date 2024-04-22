<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Assignment $assignment)
    {
        $responses = Solution::all()->where('assignment_id', $assignment->id);
        if(!isset($responses)){
            return response()->json(['error' => 'no responses to that task']);
        }
        return response()->json($responses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Solution $solution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solution $solution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solution $solution)
    {
        //
    }
}
