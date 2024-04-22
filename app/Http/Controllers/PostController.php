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
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Support\Str;

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


    public function getComments(Post $post)
    {
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
            'group_id' => ['required', Rule::exists('groups', 'id')]
        ]);

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

        if($request->hasFile('files')){
            foreach($request->file('files') as $file){
                $mimeType = $file->getClientMimeType();
                $name = $file->getClientOriginalName();

                if (!Str::contains($mimeType, 'text')  && !Str::contains($mimeType, 'pdf')) {
                    return response()->json(['error' => 'Tipo de archivo no valido']);
                }

                $path = $file->store('posts/'.$post->id);

                Post_file::create([
                    'post_id' => $post->id,
                    'file_name' => $name,
                    'file_path' => $path
                ]);
            }

        }

        return response()->json(['message' => 'post created successfully']);
    }

    public function getFile(Post $post, string $file ){

        $file = Post_file::find($file);

        if($file === null){
            return response()->json(['message' => 'file not found']);
        }
        return Storage::download($file->file_path, $file->file_name);
    }

     /* Display the specified resource.
     */
    public function show(Post $post)
    {
        $post['links'] = Post_links::all()->where('post_id', $post->id);
        $post['files'] = Post_file::all()->where('post_id', $post->id);
        $post['comments'] = $post->getComments();

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['success' => 'post successfully deleted']);
    }

    public function destroyFile(Request $request, string $file) {
        Post_file::destroy($file);
        return response()->json(['success' => 'File deleted successfully']);
    }

    public function destroyLink(Request $request, string $link) {
        Post_links::destroy($link);
        return response()->json(['success' => 'File deleted successfully']);
    }



    public function storeFile(Request $request, Post $post){

    }

    public function storeLink(Request $request, Post $post){
        $links = $request->validate([
            "links.*.link" => ['required', 'string'],
            "links.*.link_name" => ['required', 'string']
        ]);

        foreach($links['links'] as $link){
            $link['post_id'] = $post->id;
            Post_links::create($link);
        }

        return response()->json(['success' => 'Links added successfully']);
    }
}
