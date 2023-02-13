<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\Otp;
use App\Mail\OtpMailer;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public static function send($user, $method) {
        $code = rand(1111, 9999);
        $expiration = Carbon::now()->addHours(1)->format('Y-m-d H:i:s');

        $saveData = Otp::create([
            'user_id' => $user->id,
            'code' => $code,
            'method' => $method,
            'has_used' => 0,
            'expiry' => $expiration
        ]);

        $sendMail = Mail::to($user->email)->send(new OtpMailer([
            'user' => $user,
            'otp' => $saveData
        ]));
    }
}
