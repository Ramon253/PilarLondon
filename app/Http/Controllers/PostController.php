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
use http\Env\Response;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\CollectionModify;
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
            'posts' => array_values(Post::all()->where('group_id', null)->map(function ($post) {
                $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
                $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());
                return $post;
            })->toArray())
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
            'subject' => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'group_id' => ['required', Rule::exists('groups', 'id')]
        ]);

        return $this->savePost($request, $post);
    }

    public function storePublic(Request $request)
    {

        $post = $request->validate([
            'name' => ['required', 'string'],
            'subject' => ['string'],
            'description' => ['string']
        ]);
        $post['group_id'] = null;

        return $this->savePost($request, $post);
    }

    /*
     * Show
     */
    public function show(Post $post)
    {
        $commentController = new CommentController();

        $post['links'] = array_values(Post_link::all()->where('post_id', $post->id)->toArray());
        $post['files'] = array_values(Post_file::all()->where('post_id', $post->id)->toArray());
        $post['comments'] = array_values($commentController->index($post));

        try {
            $post['group_name'] = Group::findOrFail($post->group_id)->name;
        } catch (ModelNotFoundException $e) {
            $post['group_name'] = 'Public';
        }

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
            'description' => ['string']
        ]);
        if ($request->has('description')) {
            if ($postData['description'] === '--@undefined')
                $postData['description'] = null;
        }
        if ($request->group_id !== 'public') {
            $group = $request->validate([
                'group_id' => ['required', Rule::exists('groups', 'id')]
            ]);
            $postData['group_id'] = $group['group_id'];
        } else {
            $postData['group_id'] = null;
        }

        $updatedPost = $post->update($postData);
        return response()->json(['success' => 'post successfully updated', 'post' => $post]);
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


    /*
     * Privates
     * */

    private function savePost(Request $request, $post)
    {
        $post = Post::create($post);

        if ($request->has('links')) {
            $controller = new LinkController;
            $post['links'] = $controller->store($request, 'post', $post->id, new Post_link())['links'];
        }

        if ($request->hasFile('files')) {
            $controller = new FileController;
            $post['fileLinks'] = $controller->store($request, 'post', $post->id, new Post_file())['files'];
            $post['files'] = $post['fileLinks'];
        }

        return response()->json(['message' => 'post created successfully', 'post' => $post]);
    }

}
