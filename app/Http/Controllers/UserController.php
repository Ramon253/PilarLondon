<?php

namespace App\Http\Controllers;

use App\Models\Email_verification;
use App\Models\Teacher;
use App\Mail\auth as MailAuth;
use App\Models\Group;
use App\Models\Join_code;
use App\Models\Student;
use App\Models\Student_group;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use PHPUnit\Framework\MockObject\Builder\Stub;

class UserController extends Controller
{

    /**
     * Logins/logout
     */
    public function login_token(Request $request)
    {
        $identifier = self::getIdentifier($request);

        $formData = $request->validate([
            $identifier['field'] => $identifier['validation'],
            'password' => 'required'
        ]);

        if (auth()->attempt($formData)) {
            return response()->json([
                'user' => Auth::user()->createToken('hola')->plainTextToken
            ]);
        }

        return response()->json(['auth' => 'Incorrect credentials']);
    }

    public function login(Request $request)
    {

        $identifier = self::getIdentifier($request);

        $formData = $request->validate([
            $identifier['field'] => $identifier['validation'],
            'password' => 'required',

        ]);
        $rememberMe = $request['remember_me'];

        if (auth()->attempt($formData, $rememberMe)) {

            session()->regenerate();
            return response()->json(
                $this->getUserInfo($request),
            );
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }


    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }


    /**
     * Shows
     */

    public function profilePic(User $user)
    {
        try {
            $teacher = Teacher::all()->where('user_id', $user->id)->firstOrFail();
            if (Storage::has($teacher->profile_photo)) {
                return Storage::get($teacher->profile_photo);
            }
            return file_get_contents(public_path('assets/defaultProfile.png'));
        } catch (ItemNotFoundException $e) {
        }
        try {
            $student = Student::all()->where('user_id', $user->id)->firstOrFail();
            if (Storage::has($student->profile_photo ?? '')) {
                return Storage::get($student->profile_photo);
            }

            return file_get_contents(public_path('assets/defaultProfile.png'));
        } catch (ItemNotFoundException $e) {
            return file_get_contents(public_path('assets/defaultProfile.png'));
        }
    }

    public function show(Request $request)
    {
        return response()->json($this->getUserInfo($request));
    }


    /**
     * Register
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:8'
        ]);

        $user = User::create($formFields);
        $user['role'] = 'none';
        auth()->login($user);

        return response()->json(['message' => 'user created successfully', 'user' => $this->getUserInfo($request)]);
    }


    /**
     * Activations
     */

    public function verify(Request $request)
    {
        $token = $request->validate(['token' => ['required' ]])['token'];
        $email_verification = Email_verification::all()->where('user_id', auth()->id())->where('token', $token)->first();
        if ($email_verification){
            $user = User::find(\auth()->id());
            $user->email_verified_at = now();
            $user->save();
            $email_verification->delete();
            return response()->json(['success'=>'Auth passed successfully']);
        }

        return response()->json('Incorrect code', 403);
    }


    public function activate(Request $request)
    {
        $data = $request->validate([
            'join_code' => ['required', Rule::exists('join_codes', 'code')]
        ]);

        try {
            $code = Join_code::findOrFail($data['join_code']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Code not found', 404]);
        }

        if ($code->user_id !== null) {
            return response()->json(['error' => 'code already in use', 401]);
        }

        $code->user_id = auth()->id();
        $code->save();

        return response()->json(['success' => 'account successfully activated']);
    }

    public function isActivated()
    {
        try {
            $code = Join_code::where('user_id', auth()->id())->firstOrFail();
            return response()->json(['message' => 'You are acivated']);
        } catch (ModelNotFoundException|ItemNotFoundException $e) {
            return response()->json(['error' => 'You user is not activated'], 401);
        }
    }

    /**
     * Privates
     */
    private static function getIdentifier(Request $request)
    {
        $field = 'name';
        $validation = ['required'];
        $value = $request->name;
        if (isset($request->email)) {

            $field = 'email';
            $validation[] = 'email';
            $value = $request->email;
        }
        return ['field' => $field, 'validation' => $validation, 'value' => $value];
    }

    private static function sendMail($user)
    {
        $code = random_int(100000, 999999);
        session(['verificationCode' => $code]);
        session(['user' => $user]);

        Mail::to($user->email)->send(new MailAuth($code));
    }

    private function getUserInfo(Request $request)
    {
        $user = User::find(auth()->id());
        $user['role'] = $user->getRol();

        $response = [
            "user" => $user
        ];

        if ($request->has('student'))
            $response['student'] = $request['student'];

        return $response;
    }
}
