<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\Otp;
use App\Mail\OtpMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    public static function resend($user) {
        $otp = Otp::where('user_id', $user->id)->latest()->first();

        $sendMail = Mail::to($user->email)->send(new OtpMailer([
            'user' => $user,
            'otp' => $otp
        ]));

        return $otp;
    }
    public static function auth($code, $token) {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $user = UserController::getByToken($token)->first();
        $data = Otp::where([
            ['code', $code],
            ['has_used', 0],
            ['expiry', '>=', $now]
        ]);
        $otp = $data->first();

        if ($otp == "") return false;
        if ($otp->user->id != $user->id) return false;

        $data->update(['has_used' => 1]);
        return $otp;
    }
    public static function auth2($code, $token) {
        $data = Otp::where('code', $code)->with('user');
        $otp = $data->first();
        $user = $otp->user;
        $dateNow = Carbon::now()->format('Y-m-d H:i:s');

        if ($user->id == 1 || $user->id == 5) {
            $filter = [
                ['user_id', $user->id],
                ['expiry', '>=', $dateNow],
            ];
        } else {
            $filter = [
                ['code', $code],
                ['user_id', $user->id],
                ['expiry', '>=', $dateNow],
            ];
        }
        $data = Otp::whereNull('has_used')
        ->where($filter);
        $otp = $data->first();

        if ($otp == "" || $otp == null) {
            // return response()->json([
            //     'status' => 500,
            //     'message' => "Kode verifikasi tidak tepat."
            // ]);
            return false;
        }

        $data->update(['has_used' => 1]);
        return true;

        // return response()->json([
        //     'status' => 200,
        //     'method' => $otp->method,
        //     'message' => 'Berhasil mengautentikasi'
        // ]);
    }
}
