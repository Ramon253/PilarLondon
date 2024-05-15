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
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    * Indexes
    */

    public function indexPost(Post $post)
    {
        return response()->json($this->index($post));
    }

    /*
     * Stores
     */

    public function storePost(Request $request, Post $post)
    {
        return $this->store($request, 'post', $post->id, new Post_comment, $post);
    }

    public function storeAssignment(Request $request, Assignment $assignment)
    {
        return $this->store($request, 'assignment', $assignment->id, new Assignment_comment, $assignment);
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

    public function destroyPost(Post_comment $post_comment)
    {
        return $this->destroy($post_comment, Post::find($post_comment->post_id));
    }

    public function destroyAssignment(Request $request, Assignment_comment $assignment_comment)
    {
        return $this->destroy($assignment_comment, Assignment::find($assignment_comment->assignment_id));
    }


    /**
     * Privates
     */
    private function store(Request $request, string $table, string $id, Model $model, Model $tableModel)
    {
        $comment = $request->validate([
            'content' => ['required', 'string'],
            'public' => ['boolean'],
            'parent_id' => [Rule::exists($table . '_comments', 'id')]
        ]);

        $comment['user_id'] = auth()->id();
        $comment[$table . '_id'] = $id;

        $result = $model->create($comment);

        $comments = $this->index($tableModel);
        
        return response()->json([
            'success' => 'Comment successfuly created',
            'comments' => $comments
        ]);
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

    public function destroy(Model $model, Model $fromModel)
    {
        $result = $model->delete();

        if ((int) $result === 1) {
            return response()->json(
                [
                    'success' => 'Comment successfully deleted',
                    'comments' => $this->index($fromModel)
                ]
            );
        }
        return response()->json(['error' => 'Comment could not be deleted']);
    }

    public function index($model)
    {
        $comments = $model->getComments()->map(
            function ($comment) {
                $comment['role'] = User::find($comment->user_id)->getRol();
                return $comment;
            }
        );

        if (User::find(auth()->id())->getRol() !== 'teacher') {
            $yourComments = $comments->filter(
                fn ($comment) => $comment->user_id === auth()->id()
            )->pluck('id')->toArray();

            $comments = $comments->filter(
                fn ($comment) => $comment->public || $comment->user_id === auth()->id() || in_array($comment->parent_id, $yourComments)
            );
        }

        return array_values((gettype($comments) !== 'array') ? $comments->toArray() : $comments);
    }
}
