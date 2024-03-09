<?php

namespace App\Http\Controllers;

use App\Mail\auth;
use App\Models\Group;
use App\Models\Student;
use App\Models\Student_group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     *
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.register');
    }

    public function index()
    {
        $response = ['groups' => Group::all()];
        if (auth()) {
            $studentInfo = Student::isStudent(auth()->id());
            if (isset($studentInfo)) {
                $response['isStudent'] = true;
                $response['yourGroups'] = $studentInfo->getGroups();

            }
        }
        return view('welcome', $response);
    }

    public function login(Request $request)
    {
        $identifier = self::getIdentifier($request);

        $formData = $request->validate([
            $identifier['field'] => $identifier['validation'],
            'password' => 'required'
        ]);

        if (auth()->attempt($formData)) {
            $user = auth()->user();
            if (is_null($user->email_verified_at)) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                self::sendMail($user);
                return redirect('/verify');
            }
            $request->session()->regenerate();
            return redirect('/')->with('message', 'Logged in successfully');
        }

        return back()->withErrors(['password' => 'Invalid username/email or password']);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
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

        Mail::to($user->email)->send(new auth($code));
    }

    public function getVerify()
    {
        if (session()->missing('verificationCode')) {
            return redirect('/')->with('message', 'No has figura ningun intento de verificacion');
        }
        return view('user.verify');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6'
        ]);
        $formFields['password'] = password_hash($formFields['password'], PASSWORD_DEFAULT);
        $user = User::create($formFields);

        return redirect('/login')->with('message', 'usuario creado con exito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Users $users)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Users $users)
    {
        //
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
