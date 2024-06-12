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
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Pest\Laravel\json;
use function PHPUnit\Framework\throwException;

class FileController extends Controller
{
    private array $allowedMimetypes = ['image', 'text', 'audio', 'video', 'pdf'];

    /**
     * Shows
     */

    public function showAssignment(Assignment_file $assignment_file)
    {
        return response()->json($assignment_file);
    }

    public function showPost(Post_file $post_file)
    {
        return response()->json($post_file);
    }

    public function showSolution(Solution_file $solution_file)
    {
        return response()->json($solution_file);
    }

    /**
     * Downloads
     */


    public function downloadAssignment(Assignment_file $assignment_file)
    {
        return Storage::download($assignment_file->file_path, $assignment_file->file_name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$assignment_file->file_name.'"',
        ]);
    }

    public function downloadPost(Post_file $post_file)
    {
        return Storage::download($post_file->file_path, $post_file->file_name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$post_file->file_name.'"',
        ]);
    }

    public function downloadSolution(Solution_file $solution_file)
    {
        return Storage::download($solution_file->file_path, $solution_file->file_name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$solution_file->file_name.'"',
        ]);
    }

    public function getAssignment(Assignment_file $assignment_file)
    {
        return Storage::get($assignment_file->file_path);
    }

    public function getPost(Post_file $post_file)
    {
        return Storage::get($post_file->file_path);
    }

    public function getSolution(Solution_file $solution_file)
    {
        return Storage::get($solution_file->file_path);
    }

    /**
     * Store
     */
    public function storeAssignment(Request $request, Assignment $assignment)
    {
        return response()->json($this->store($request, 'assignment', $assignment->id, new Assignment_file));
    }


    public function storePost(Request $request, Post $post)
    {
        return response()->json($this->store($request, 'post', $post->id, new Post_file));
    }


    public function storeSolution(Request $request, Solution $solution)
    {
        return response()->json($this->store($request, 'solution', $solution->id, new Solution_file));
    }

    /**
     * Destroys
     */

    public function destroyPost(Post_file $post_file)
    {
        return response()->json($this->destroy($post_file));
    }

    public function destroyAssignment(Assignment_file $assignment_file)
    {
        return response()->json($this->destroy($assignment_file));
    }

    public function destroySolution(Solution_file $solution_file)
    {
        return response()->json($this->destroy($solution_file));
    }

    /**
     * Private
     */
    public function store(Request $request, string $table, string $id, Model $model)
    {
        if (!$request->has('files')) {
            return ['error' => 'No files sended'];
        }
        $files = [];

        foreach ($request->file('files') as $file) {
            $mimeType = $file->getClientMimeType();
            $name = $file->getClientOriginalName();
            $isAllowed = false;

            foreach ($this->allowedMimetypes as $allowedMimetype) {
                if (Str::contains($mimeType, $allowedMimetype)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                throwException(new \Exception('File is not allowed'));
            }


            $path = $file->store($table . "s/$id");

            $createdFile = $model::create([
                $table . '_id' => $id,
                'file_name' => $name,
                'file_path' => $path,
                'mime_type' => $mimeType,
                'header' => $request['header']
            ]);
            $files[] = $createdFile;
        }
        return
            [
                'success' => 'Files successfully uploaded',
                'files' => array_values($files)
            ];
    }

    private function destroy(Model $object): array
    {
        $result = Storage::delete($object->file_path);
        $object->delete();

        return ((int)$result === 1) ? ['success' => 'File destroyed successfully'] : ['Error' => 'Error deleteing the file'];
    }
}
