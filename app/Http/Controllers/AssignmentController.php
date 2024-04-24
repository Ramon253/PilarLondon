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
        $assignment['links'] = Assignment_links::all()->where('assignment_id', $assignment->id);
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
            $this->saveLinks($request, $assignment);
        }

        if ($request->hasFile('files')) {
            $this->saveFiles($request, $assignment);
        }

        return response()->json(['message' => 'assignment created successfully']);
    }

    public function storeFile(Request $request, Assignment $assignment)
    {
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'You must include a file to insert a file']);
        }

        if ($this->saveFiles($request, $assignment)) {
            return response()->json(['success' => 'Files successfully uploaded'], 200);
        }
        return response()->json(['error' => 'Tipo de archivo no valido']);
    }

    public function storeLink(Request $request, Assignment $assignment)
    {
        if ($this->saveLinks($request, $assignment)) {
            return response()->json(['success' => 'Links successfully uploaded']);
        }
        return response()->json(['error' => 'Error while saving links']);
    }

    public function storeComment(Request $request, Assignment $assignment)
    {
        $comment = $request->validate([
            'content' => ['required', 'string'],
            'public' => ['boolean'],
            'parent_id' => [Rule::exists('Assignment_comments', 'id')]
        ]);
        $comment['user_id'] = auth()->id();
        $comment['assignment_id'] = $assignment->id;

        Assignment_comment::create($comment);

        return response()->json(['success' => 'Comment successfuly created']);
    }



    /**
     * Destroy functions.
     */
    public function destroy(Assignment $assignment)
    {
        /*
        $files = Assignment_file::all()->where('assignment_id', $assignment->id);
        foreach ($files as $file) {

            Storage::delete($file->file_path);
        }*/
        Storage::deleteDirectory("assignments/".$assignment->id);
        $assignment->delete();
        return response()->json(['message' => 'assignment deleted successfully']);
    }

    public function destroyFile(Assignment_file $assignment_file)
    {
        Storage::delete($assignment_file->file_path);
        $assignment_file->delete();
        return response()->json([
            'success' => 'file destroyed successfully'
        ]);
    }

    public function destroyLink(Assignment_links $assignment_link)
    {
        $assignment_link->delete();
        return response()->json(['success' => 'Link successfully deleted'], 200);
    }

    public function destroyComment(Assignment_comment $assignment_comment)
    {
        $assignment_comment->delete();
        return response()->json(['success' => 'Comment deleted successfully']);
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

    public function updateComment(Request $request, Assignment_comment $assignment_comment)
    {
        $comment = $request->validate([
            'content' => ['string'],
            'public' => ['boolean']
        ]);
  
        $assignment_comment->update($comment);

        return response()->json(['success' => 'Comment successfully updated']);
    }


    /**
     * Private functions
     */

    private function saveFiles(Request $request, Assignment $assignment): bool
    {

        foreach ($request->file('files') as $file) {
            $mimeType = $file->getClientMimeType();
            $name = $file->getClientOriginalName();

            if (!Str::contains($mimeType, 'text')  && !Str::contains($mimeType, 'pdf') && !Str::contains($mimeType, 'image')) {
                return false;
            }

            $path = $file->store('assignments/' . $assignment->id);

            Assignment_file::create([
                'assignment_id' => $assignment->id,
                'file_name' => $name,
                'file_path' => $path
            ]);
        }
        return true;
    }

    private function saveLinks(Request $request, Assignment $assignment)
    {

        $links = $request->validate([
            "links.*.link" => ['required', 'string'],
            "links.*.link_name" => ['required', 'string']
        ]);

        foreach ($links['links'] as $link) {
            $link['assignment_id'] = $assignment->id;
            Assignment_links::create($link);
        }

        return true;
    }
}
