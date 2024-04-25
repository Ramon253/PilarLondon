<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Assignment_file;
use App\Models\Assignment_link;
use App\Models\Post;
use App\Models\Post_file;
use App\Models\Post_link;
use App\Models\Solution;
use App\Models\Solution_file;
use App\Models\Solution_link;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LinkController extends Controller
{

    /**
     * Shows
     */

    public function showAssignment(Assignment_link $assignment_link){
        return response()->json($assignment_link);
    }

    public function showPost(Post_link $post_link){
        return response()->json($post_link);
    }

    public function showSolution(Solution_link $solution_link){
        return response()->json($solution_link);
    }

    /**
     * Store
     */
    public function storeAssignment(Request $request, Assignment $assignment)
    {
        return $this->store($request, 'assignment', $assignment->id, new Assignment_link);
    }


    public function storePost(Request $request, Post $post)
    {
        return $this->store($request, 'post', $post->id, new Post_link);
    }


    public function storeSolution(Request $request, Solution $solution)
    {
        return $this->store($request, 'solution', $solution->id, new Solution_link);
    }

    /**
     * Destroys
     */

    public function destroyPost(Post_link $post_link)
    {
        return response()->json($this->destroy($post_link));
    }

    public function destroyAssignment(Assignment_link $assignment_link)
    {
        return response()->json($this->destroy($assignment_link));
    }

    public function destroySolution(Solution_link $solution_link)
    {
        return response()->json($this->destroy($solution_link));
    }

    /**
     * Private
     */
    private function store(Request $request, string $table, string $id, Model $model)
    {
        $links = $request->validate([
            "links.*.link" => ['required', 'string'],
            "links.*.link_name" => ['required', 'string']
        ]);

        foreach ($links['links'] as $link) {
            $link[$table. '_id'] = $id;
            $model::create($link);
        }
        return response()->json(['success' => 'Links successfully uploaded', 'link' => $links], 200);
    }

    private function destroy(Model $object): array
    {
        $result = $object->delete();
        return ((int) $result === 1) ? ['success' => 'Link destroyed successfully'] : ['Error' => 'Error deleteing the link'];
    }
}
