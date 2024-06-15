<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function studentCode(){}
}
