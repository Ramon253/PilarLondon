<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Models\Student_group;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StudentController extends Controller
{

    /**
     * Store 
     */
    public function store( Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'required',
            'surname' => 'required',
            'level' => 'required',
            'birth_date' => ['required', 'date'],
            'parent_id' => [Rule::exists('users', 'id')]
        ]);

        $formData['user_id'] = auth()->id();
        $formData['birth_date'] = Carbon::parse($formData['birth_date']);
        
        if ($request->hasFile('profile_photo')){
            $formData['profile_photo'] = $this->storePhoto($request);
        }

        $student = Student::create($formData);

        return response()->json(['success' => 'Welcome to pilar london']);
    }


    /**
     * Updates
     */
    public function update(Request $request)
    {
        $formData = $request->validate([
            'full_name' => 'string',
            'surname' => 'string',
            'level' => 'string',
            'birth_date' => ['date'],
            'parent_id' => [Rule::exists('users', 'id')]
        ]);

        if ($request->hasFile('profile_photo')) {
            Storage::delete($request['student']->profile_photo);
            $formData['profile_photo'] = $this->storePhoto($request);
        }
        $request['student']->update($formData);

        return response()->json(['success' => 'Profile updated successfully']);
    }


    /**
     * Destroy
     */

    public function destroy(Request $request, Student $student)  {
        Storage::deleteDirectory('users/'.auth()->id());
        $student->delete();
        return response()->json(['success' =>'Student deleted successfully']);
    }

    /**
     * Privates
     */

    private function storePhoto(Request $request)  {
        $file = $request->file('profile_photo');   
        $mimeType = $file->getClientMimeType();

        if(!Str::contains($mimeType, 'image')){
            return response()->json(['error' => 'Invalid file, please send an image']);
        }

        return $file->storeAs('users/'.auth()->id());
    }
}
