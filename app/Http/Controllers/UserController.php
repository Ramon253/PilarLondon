<?php

namespace App\Http\Controllers;

use App\Mail\auth as MailAuth;
use App\Models\Group;
use App\Models\Student;
use App\Models\Student_group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Builder\Stub;

class UserController extends Controller
{

    public function login_token(Request $request) {
        $identifier = self::getIdentifier($request);

        $formData = $request->validate([
            $identifier['field'] => $identifier['validation'],
            'password' => 'required'
        ]);
        $remmberMe = isset($request->remember_me);

        if (auth()->attempt($formData, $remmberMe)) {
            return response()->json([
                'user' => Auth::user()->createToken('hola')->plainTextToken
            ]);
        }

        return response()->json(['auth' => 'Incorrect credentials']);
    }

    public function  dashboard() {
        $student = Student::isStudent(auth()->id());
        $groups = $student->getGroups();
        return response()->json([
            'student' => $student
        ]);
    }

    public function login(Request $request)
    {

        $identifier = self::getIdentifier($request);

        $formData = $request->validate([
            $identifier['field'] => $identifier['validation'],
            'password' => 'required'
        ]);
        $remmberMe = isset($request->remember_me);


        if (auth()->attempt($formData, $remmberMe)) {

            /*
            if (is_null($user->email_verified_at)) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                self::sendMail($user);
                return redirect('/verify');
            }*/
            //request->session()->regenerate();
            session()->regenerate();
            return response()->json([
                'user' => $request->user(),
            ]);
        }

        return response()->json(['auth' => 'Incorrect credentials']);
    }

    public function show() {
        $student = Student::isStudent(auth()->id());
        $response = [
            "user" => auth()->user()
        ];

        if (isset($student))
            $response['student'] = $student;

        return response()->json($response);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        auth()->user();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function verify(Request $request)
    {
        $code = $request->validate(['code' => 'required'])['code'];
        if (session()->missing('verificationCode')) {
            return redirect('/')->with('message', 'There it is no login attempt in this session');
        }
        if (session('verificationCode') !== (int)$code) {
            return back()->with('message', 'Incorrect code');
        }
        auth()->login(session('user'));
        $request->session()->regenerate();

        $user = session('user');
        $user->email_verified_at = now();
        $user->save();

        return redirect('/')->with('message', 'Auth passes successfully');
    }

    /**
     * @throws RandomException
     */
    private static function sendMail($user)
    {
        $code = random_int(100000, 999999);
        session(['verificationCode' => $code]);
        session(['user' => $user]);

        Mail::to($user->email)->send(new MailAuth($code));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:8'
        ]);

        $user = User::create($formFields);

        return response()->json(['message' => 'user created successfully']);
    }

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
}
