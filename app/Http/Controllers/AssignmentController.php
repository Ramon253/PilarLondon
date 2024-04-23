<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Assignment;
use App\Http\Controllers\Controller;
use App\Models\Assignment_comment;
use App\Models\Assignment_file;
use App\Models\Assignment_links;
use App\Models\Group;
use App\Models\Post_file;
use App\Models\Student_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
/*        $groups = Student_group::all()->where('student_id', $request['student']->id)->pluck('group_id');
        $assignments = Assignment::all()->whereIn('group_id', $groups);*/
        $assignments = Assignment::all();
        return response()->json($assignments);
    }

    /**
     * Store a newly created resource in storage.
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

        if($request->has('links')){

            $links = $request->validate([
                "links.*.link" => ['required', 'string'],
                "links.*.link_name" => ['required', 'string']
            ]);

            foreach($links['links'] as $link){
                $link['assignment_id'] = $assignment->id;
                Assignment::create($link);
            }

        }
        if($request->hasFile('files')){
            foreach($request->file('files') as $file){
                $mimeType = $file->getClientMimeType();
                $name = $file->getClientOriginalName();

                if (!Str::contains($mimeType, 'text')  && !Str::contains($mimeType, 'pdf')) {
                    return response()->json(['error' => 'Tipo de archivo no valido']);
                }

                $path = $file->store('assignments/'.$assignment->id);

                Assignment_file::create([
                    'assignment_id' => $assignment->id,
                    'file_name' => $name,
                    'file_path' => $path
                ]);
            }

        }

        return response()->json(['message' => 'assignment created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $assignment['files'] = Assignment_file::all()->where('assignment_id', $assignment->id);
        $assignment['links'] = Assignment_links::all()->where('assignment_id', $assignment->id);
        $assignment['comments'] = Assignment_comment::all()->where('assignment_id', $assignment->id);

        return response()->json($assignment);
    }

    /**
     * Update the specified resource in storage.
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $files = Assignment_links::all()->where('assignment_id', $assignment->id);
        foreach($files as $file){
            Storage::delete($file->file_path);
        }
        $assignment->delete();
        return response()->json(['message' => 'assignment deleted successfully']);
    }
}
