<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Mail\auth;
use App\Models\Post_comment;
use App\Models\Post_file;
use App\Models\Post_link;
use App\Models\User;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Support\Str;

use function Pest\Laravel\json;
use function PHPUnit\Framework\returnSelf;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::all()->map(function ($group) {
            return ['id' => $group->id, 'name' => $group->name];
        });
        return response()->json([
            'groups' => $groups,
            'posts' => Post::all()->map(function ($post) {
                $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
                $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());
                $post['group_name'] = Group::find($post->group_id)->name;
                return $post;
            })
        ]);
    }

    /**
     * Store
     */
    public function store(Request $request, Group $group)
    {
        $request['group_id'] = $group->id;

        $post = $request->validate([
            'name' => ['required', 'string'],
            'subject' => ['string'],
            'description' => ['string'],
            'group_id' => ['required', Rule::exists('groups', 'id')]
        ]);


        $post = Post::create($post);

        if ($request->has('links')) {
            $controller = new LinkController();
            $result = $controller->storePost($request, $post)->getData(true);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }
        }

        if ($request->hasFile('files')) {
            $controller = new FileController();
            $result = $controller->storePost($request, $post)->getContent(true);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }
        }

        return response()->json(['message' => 'post created successfully']);
    }


    /*
     * Show
     */
    public function show(Post $post)
    {
        $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
        $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());

        $comments = $post->getComments()->map(
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

        $post['comments'] = array_values($comments->toArray());
        $post['group_name'] = Group::find($post->group_id)->name;

        $post['groups'] = Group::all()->map(function ($group) {
            return ['id' => $group->id, 'name' => $group->name];
        });

        return response()->json($post);
    }

    /**
     * Update
     */
    public function update(Request $request, Post $post)
    {
        $postData = $request->validate([
            'name' => ['string'],
            'subject' => ['string'],
            'description' => ['string'],
            'group_id' => [Rule::exists('groups', 'id')]
        ]);
        $updatedPost =  $post->update($postData);
        return response()->json(['success' => 'post successfully updated']);
    }

    /**
     * Delete
     */
    public function destroy(Post $post)
    {
        Storage::deleteDirectory("posts/" . $post->id);
        $post->delete();
        return response()->json(['success' => 'post successfully deleted']);
    }
}
