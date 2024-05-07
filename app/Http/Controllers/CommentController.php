<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Assignment_comment;
use App\Models\Post;
use App\Models\Post_comment;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * shows .
     */
    public function showPost(Post_comment $post_comment)
    {
        return response()->json([
            'comment' => $post_comment,
            'responses' => post_comment::all()->where('parent_id', $post_comment->id)
        ]);
    }

    public function showAssignment(Assignment_comment $assignment_comment)
    {
        return response()->json([
            'comment' => $assignment_comment,
            'responses' => Assignment_comment::all()->where('parent_id', $assignment_comment->id)
        ]);
    }


    /*
     * Stores
     */

    public function storePost(Request $request, Post $post)
    {
        return $this->store($request, 'post', $post->id, new Post_comment);
    }

    public function storeAssignment(Request $request, Assignment $assignment)
    {
        return $this->store($request, 'assignment', $assignment->id, new Assignment_comment);
    }

    public function storeRespone(Request $request, Solution $response)
    {
    }

    /**
     * Updates
     */

    public function updatePost(Request $request, Post_comment $post_comment)
    {
        return $this->update($request, $post_comment);
    }

    public function updateAssignment(Request $request, Assignment_comment $assignment_comment)
    {
        return $this->update($request, $assignment_comment);
    }


    /**
     * Destroys
     */

    public function destroyPost(Request $request, Post_comment $post_comment)
    {
        return $this->destroy($post_comment);
    }

    public function destroyAssignment(Request $request, Assignment_comment $assignment_comment)
    {
        return $this->destroy($assignment_comment);
    }


    /**
     * Privates
     */
    private function store(Request $request, string $table, string $id, Model $model)
    {
        $comment = $request->validate([
            'content' => ['required', 'string'],
            'public' => ['boolean'],
            'parent_id' => [Rule::exists($table. '_comments', 'id')]
        ]);

        $comment['user_id'] = auth()->id();
        $comment[$table . '_id'] = $id;

        $result = $model->create($comment);

        return response()->json(['success' => 'Comment successfuly created', 'comment' => $result]);
    }

    public function update(Request $request, Model $model)
    {
        $comment = $request->validate([
            'content' => ['string'],
            'public' => ['boolean'],
        ]);

        $result = $model->update($comment);
        if ((int)$result === 1) {
            return response()->json(['success' => 'Comment successfuly updated']);
        }

        return response()->json(['error' => 'Comment could not be updated']);
    }

    public function destroy(Model $model)
    {
        $result = $model->delete();

        if ((int) $result === 1) {
            return response()->json(['success' => 'Comment successfully deleted']);
        }
        return response()->json(['error' => 'Comment could not be deleted']);
    }
}
