<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\auth;
use App\Mail\Contact;
use App\Models\Email_verification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailController extends Controller
{


    public function contact(Request $request)
    {
        $mail = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:50'],
            'subject'=> ['max:100'],
            'phone' => [],
            'email' => ['required','email']
        ]);

        Mail::to('pilarlondoncontact@gmail.com')->send(new Contact($mail));
        return response()->json(['success' => 'Mail successfully sent']);
    }

    public function studentCode(Request $request){
        $mail = $request->validate([
            'email' => ['required', 'email']
        ])['email'];

        Mail::to($mail)->send(new auth(TeacherController::generateStudent()));
        return response()->json(['success' => 'Mail successfully sent']);
    }

    public function isVerified(Request $request){
        if ($request->user()->hasVerifiedEmail()){
            return response()->json(['verified' => 'Your email is already verified']);
        }

        $code = '';
        do {
            $code = Str::random(25);
            $model = Email_verification::all()->where('token', $code)->first();
        } while ($model !== null);

        $email = Email_verification::all()->where('user_id', auth()->id())->first();
        if ($email){
            $email->token = $code;
            $email->save();
        }else
            $email = Email_verification::create([
                'token' => $code,
                'user_id' => auth()->id()
            ]);

        Mail::to($request->user()->email)->send(new auth($code));

        return response()->json(['unverified' => 'No estas verificado, hemos enviado un email de verificacion'], 403);
    }
}
