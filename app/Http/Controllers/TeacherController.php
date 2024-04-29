<?php

namespace App\Http\Controllers;

use App\Models\Join_code;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Teacher::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $join_code = Join_code::all()->where('user_id', auth()->id())->firstOrFail();
            if ($join_code->role !== 'teacher') {
                return response()->json(['error' => 'You cant be student, your role is' . $join_code->role]);
            }
        } catch (ItemNotFoundException $e) {
            return response()->json(['error' => 'You are not allowed to yet to join the class'], 400);
        }
    }

    public static function generateStudent(Request $request)
    {
        $code = '';
        do {
            $code = Str::random(50);
            $model = Join_code::find($code);
        } while ($model !== null);

        $join_code = Join_code::create([
            'code' => $code,
            'role' => 'student'
        ]);
        return $code;
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}
