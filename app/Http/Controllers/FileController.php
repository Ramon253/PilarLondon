<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Assignment_file;
use App\Models\Post;
use App\Models\Post_file;
use App\Models\Solution;
use App\Models\Solution_file;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{

    /**
     * Store
     */
    public function storeAssignment(Request $request, Assignment $assignment)  {

        if(!$request->has('files')){
            return response()->json(['error' => 'No files sended']);
        }

        $files = $this->store($request, 'assignment', $assignment->id);

        foreach($files as $file){
            Assignment_file::create($file);
        }

        return response()->json(['success' => 'Files successfully uploaded'], 200);
    }


    public function storePost(Request $request, Post $post)  {

        if(!$request->has('files')){
            return response()->json(['error' => 'No files sended']);
        }

        $files = $this->store($request, 'post', $post->id);

        foreach($files as $file){
            Post_file::create($file);
        }

        return response()->json(['success' => 'Files successfully uploaded'], 200);
    }


    public function storeSolution(Request $request, Solution $solution)  {

        if(!$request->has('files')){
            return response()->json(['error' => 'No files sended']);
        }

        $files = $this->store($request, 'response', $solution->id);

        foreach($files as $file){
            Solution_file::create($file);
        }

        return response()->json(['success' => 'Files successfully uploaded'], 200);
    }

    /**
     * Destroys
     */

     public function destroyPost(Post_file $post_file) {
        $this->destroy($post_file);
     }

     public function destroyAssignment(Assignment_file $assignment_file) {
        $this->destroy($assignment_file);
     }

     public function destroySolution(Assignment_file $assignment_file) {
        $this->destroy($assignment_file);
     }

    /**
     * Private
     */
    private function store(Request $request, string $table, string $id):bool | array
    {
        $files = [];
        foreach ($request->file('files') as $file) {
            $mimeType = $file->getClientMimeType();
            $name = $file->getClientOriginalName();

            if (!Str::contains($mimeType, 'text')  && !Str::contains($mimeType, 'pdf') && !Str::contains($mimeType, 'image')) {
                return false;
            }
            
            $path = $file->store($table. "s/$id");

            $files[] = [
                $table .'_id' => $id,
                'file_name' => $name,
                'file_path' => $path
            ];
        }
        return $files;
    }

    private function destroy(Model $object):array
    {
        $result = Storage::delete($object->file_path);
        $object->delete();

        return ($result === 1)? ['success' => 'File destroyed successfully'] :['Error' => 'Error deleteing the file'] ;
    }
   }
