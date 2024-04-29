<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\Post_comment;
use App\Models\Post_file;
use App\Models\Post_link;
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
        return response()->json(Post::all());
    }

    /**
     * Store
     */
    public function store(Request $request, Group $group)
    {
        $request['group_id'] = $group->id;
        $post = $request->validate([
            'name' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'description' => ['string'],
            'group_id' => ['required', Rule::exists('groups', 'id')]
        ]);

        $post = Post::create($post);

        if($request->has('links')){
            $controller = new LinkController();
            $result = $controller->storePost($request, $post);

            if (isset($result['error'])){
                return response()->json($result, 400);
            }
        }

        if($request->hasFile('files')){
            $controller = new FileController();
            $result = $controller->storePost($request, $post)->getContent();

            if (isset($result['error'])){
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
        $post['links'] = Post_link::all()->where('post_id', $post->id);
        $post['files'] = Post_file::all()->where('post_id', $post->id);
        $post['comments'] = $post->getComments();

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
        ]);
        $post->update($postData);
        return response()->json(['success' => 'post successfully updated']);
    }

    /**
     * Delete
     */
    public function destroy(Post $post)
    {
        Storage::deleteDirectory("posts/".$post->id);
        $post->delete();
        return response()->json(['success' => 'post successfully deleted']);
    }

}
