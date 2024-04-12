<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\Post_comment;
use App\Models\Post_file;
use App\Models\Post_links;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\json;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Post::all());
    }


    public function getComments(Post $post)  {
        return response()->json([
            'comments' => $post->getComments()
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = $request->validate([
            'name' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'description' => ['string'],
            'group_id' => ['required',Rule::exists('groups', 'id')]
        ]);

        /*
        $post = Post::create($post);

        if($request->has('links')){

            $links = $request->validate([
                "links.*.link" => ['required', 'string'],
                "links.*.link_name" => ['required', 'string']
            ]);

            foreach($links['links'] as $link){   
                $link['post_id'] = $post->id;
                Post_links::create($link);
            }    

        }
        */
        $num = 0;
        if($request->hasFile('files')){
            return response($request->file('files')[2]);
            foreach($request->file('files') as $file){
            }
            return response()->json($num);
        }

        return response()->json(['message' => 'post created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post['links'] = Post_links::all()->where('post_id', $post->id);
        $post['files'] = Post_file::all()->where('post_id' , $post->id);
        $post['comments'] = $post->getComments();

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}