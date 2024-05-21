<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Assignment;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Http\Middleware\studentGroup;
use App\Models\Assignment_comment;
use App\Models\Assignment_file;
use App\Models\Assignment_link;
use App\Models\Group;
use App\Models\Post_file;
use App\Models\Solution;
use App\Models\Solution_file;
use App\Models\Solution_link;
use App\Models\Student_group;
use App\Models\User;
use http\Env\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use function PHPUnit\TestFixture\func;
use function Webmozart\Assert\Tests\StaticAnalysis\allAlnum;

class AssignmentController extends Controller
{

    /**Getters  */
    public function index(Request $request)
    {
        if (!$request['teacher']) {
            $student = $request['student'];
            $groups = $student->getGroups();
            $assignments = Assignment::all()->whereIn('group_id', $groups->map(fn($group) => $group['id']))
                ->map(function ($assignment) use ($student, $groups) {
                    $assignment['fileLinks'] = array_values(Assignment_file::all()->where('assignment_id', $assignment->id)->toArray());
                    $assignment['links'] = array_values(Assignment_link::all()->where('assignment_id', $assignment->id)->toArray());
                    $assignment['group_name'] = $groups->filter(fn($group) => $group->id === $assignment['group_id'])[0]->name;
                    try {
                        Solution::all()->where('assignment_id', $assignment->id)->where('student_id', $student->id)->firstOrFail();
                        $assignment['resolved'] = true;
                    } catch (ModelNotFoundException|ItemNotFoundException $e) {
                        $assignment['resolved'] = false;
                    }
                    return $assignment;
                });
            return response()->json(['assignments' => array_values($assignments->toArray())]);
        }
        $assignments = array_values(Assignment::all()->map(function ($assignment) {
            $assignment['group_name'] = Group::find($assignment->group_id)->name;
            $assignment['files'] = array_values(Assignment_file::all()->where('assignment_id', $assignment->id)->toArray());
            $assignment['links'] = array_values(Assignment_link::all()->where('assignment_id', $assignment->id)->toArray());
            return $assignment;
        })->toArray());
        return response()->json(
            [
                'groups' => Group::all(),
                'assignments' => $assignments
            ]
        );
    }

    public function show(Assignment $assignment, Request $request)
    {
        $group = Group::find($assignment->group_id);
        $assignment['groups'] = array_values(Group::all()->map(function ($group) {
            $result['name'] = $group['name'];
            $result['id'] = $group['id'];
            return $result;
        })->toArray());
        $commentController = new CommentController();
        $assignment['fileLinks'] = array_values(Assignment_file::all()->where('assignment_id', $assignment->id)->toArray());
        $assignment['links'] = array_values(Assignment_link::all()->where('assignment_id', $assignment->id)->toArray());
        $assignment['comments'] = array_values($commentController->index($assignment));
        $assignment['group_name'] = $group->name;

        if (!$request['teacher']) {
            try {
                $solution =
                    Solution::all()
                        ->where('assignment_id', $assignment->id)
                        ->where('student_id', $request['student']->id)
                        ->firstOrFail();
                $solution['fileLinks'] = array_values(Solution_file::all()->where('solution_id', $solution->id)->toArray());
                $solution['links'] = array_values(Solution_link::all()->where('solution_id', $solution->id)->toArray());
                $assignment['solution'] = $solution;
            } catch (ModelNotFoundException|ItemNotFoundException $e) {
                return response()->json($assignment);
            }
        } else {
            $assignment['solutions'] = array_values(Solution::all()->where('assignment_id', $assignment->id)->map(
                function ($solution) {
                    $student = Student::find($solution->student_id);
                    $solution['student_name'] = $student->full_name;
                    $solution['user_id'] = $student->user_id;
                    return $solution;
                }
            )->toArray());
        }

        return response()->json($assignment);
    }


    /**
     * Store functions
     */

    public function store(Request $request, Group $group)
    {
        $request['group_id'] = $group->id;
        $assignment = $request->validate([
            'name' => ['required', 'string'],
            'dead_line' => ['required', 'date'],
            'description' => ['string'],
            'group_id' => ['required', Rule::exists('groups', 'id')],
            'inClass' => ['boolean']
        ]);

        $assignment = Assignment::create($assignment);

        if ($request->has('links')) {
            $controller = new LinkController;
            $assignment['links'] = $controller->store($request, 'assignment', $assignment->id, new Assignment_link())['links'];
        }

        if ($request->hasFile('files')) {
            $controller = new FileController;
            $assignment['fileLinks'] = $controller->store($request, 'assignment', $assignment->id, new Assignment_file())['files'];
        }

        return response()->json($assignment);
    }


    /**
     * Destroy functions.
     */
    public function destroy(Assignment $assignment)
    {
        Storage::deleteDirectory("assignments/" . $assignment->id);
        $assignment->delete();
        return response()->json(['message' => 'assignment deleted successfully']);
    }


    /**
     * Update
     */
    public function update(Request $request, Assignment $assignment)
    {
        $assignmentData = $request->validate([
            'name' => ['string'],
            'dead_line' => ['string'],
            'description' => ['string'],
            'group_id' => [Rule::exists('groups', 'id')]
        ]);

        $assignment->update($assignmentData);
        return response()->json(['success' => 'post successfully updated']);
    }
}
