<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BankController;
use Str;
use Hash;
use App\Models\User;
use App\Models\UserPremium;
use App\Models\VisitorOrder as Order;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\UserController as ControllersUserController;
use App\Models\UserWithdraw;
use Carbon\Carbon;
use Xendit\Xendit as Xendit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login(Request $request) {
        $user = null;
        $status = 200;
        $message = "Berhasil login";
        $email = $request->email;

        $query = User::where('email', $request->email);
        $user = $query->first();

        if ($user != "") {
            if (Hash::check($request->password, $user->password)) {
                if ($user->is_active == 0) {
                    $status = 500;
                    $message = "Akun Anda belum aktif";
                } else {
                    $token = Str::random(32);
                    $updateToken = $query->update(['token' => $token]);
                    $user->token = $token;
                }
            } else {
                $status = 500;
                $message = "Kombinasi email dan password tidak tepat";
            }
        } else {
            $status = 500;
            $message = "Kami tidak dapat menemukan akun Anda. Coba gunakan alamat email lainnya";
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'user' => $user
        ]);
    }
    public function register(Request $request) {
        $username = Str::random(8);
        $token = Str::random(32);

        $saveData = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'photo' => "default",
            'cover' => "default",
            'accent_color' => "#dd1210",
            'font_family' => "Inter",
            'is_active' => 0,
            'token' => $token
        ]);

        OtpController::send($saveData, 'register');

        return response()->json([
            'token' => $token,
            'action' => 'register',
            'status' => 200
        ]);
    }
    public function forgetPassword(Request $request) {
        $u = User::where('email', $request->email);
        $user = $u->first();
        if ($user == "") {
            return response()->json([
                'status' => 500,
                'message' => "Kami tidak dapat menemukan akun Anda"
            ]);
        } else {
            $otp = OtpController::send($user, 'password');
            $token = Str::random(32);
            $update = $u->update(['token' => $token]);
            
            return response()->json([
                'token' => $token,
                'action' => 'password',
                'status' => 200
            ]);
        }
    }
    public function otpAuth(Request $request) {
        $code = $request->code;
        $authenticated = OtpController::auth($code, $request->token);

        $status = 500;
        if ($authenticated) {
            $status = 200;

            if ($authenticated->method == "register") {
                $activateUser = ControllersUserController::getByToken($request->token)->update([
                    'is_active' => 1,
                ]);
            }
        }

        return response()->json([
            'status' => $status,
            'otp' => $authenticated
        ]);
    }
    public function resendOtp(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->first();
        $resend = OtpController::resend($user);
        
        return response()->json([
            'status' => 200,
            'otp' => $resend
        ]);
    }
    public function update(Request $request) {
        $query = User::where('token', $request->token);
        $user = $query->first();

        $toUpdate = [
            'name' => $request->name,
            'bio' => $request->bio,
        ];

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFileName = $photo->getClientOriginalName();
            $photoNewName = $user->id . "_" . $photoFileName;
            $photo->storeAs('public/user_photos', $photoNewName);
            $toUpdate['photo'] = $photoNewName;
        }

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverFileName = $cover->getClientOriginalName();
            $coverNewName = $user->id . "_" . $coverFileName;
            $cover->storeAs('public/user_covers', $coverNewName);
            $toUpdate['cover'] = $coverNewName;
        }

        $updateUser = $query->update($toUpdate);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil memperbarui profile"
        ]);
    }
    public function home(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->first();

        $socials = SocialController::get([['user_id', $user->id]])->get();
        $products = ProductController::get([['user_id', $user->id]])->with('images')->get();

        return response()->json([
            'status' => 200,
            'socials' => $socials,
            'user' => $user,
            'products' => $products
        ]);
    }
    public function profile(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->with('premium')->first();

        return response()->json([
            'user' => $user,
        ]);
    }
    public function getPremium(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->with('premium')->first();
        $now = Carbon::now();
        $pricing = config('inhotel')['package_pricing'];

        if ($user->premium != null && $user->premium->status == 'success') {
            $activeUntil = Carbon::parse($user->premium->active_until);
            $startDate = $now <= $activeUntil ? $activeUntil : $now;
        } else {
            $startDate = $now;
        }
        $monthQuantity = $request->plan == 'monthly' ? 1 : 12;
        if ($request->plan == "monthly") {
            $monthQuantity = 1;
            $amount = $pricing['monthly'];
        } else {
            $monthQuantity = 12;
            $amount = $pricing['yearly'];
        }
        $newExpiration = $startDate->addMonths($monthQuantity);

        $externalID = "DHID_SUBS_".Str::random(12);
        $secretKey = env('XENDIT_MODE') == 'sandbox' ? env('XENDIT_SECRET_KEY_SANDBOX') : env('XENDIT_SECRET_KEY');

        Xendit::setApiKey($secretKey);
        $createInvoice = \Xendit\Invoice::create([
            'external_id' => $externalID,
            'payer_email' => $user->email,
            'description' => "InHotel " . $request->plan . " package",
            'amount' => $amount,
            'customer' => [
                'given_names' => $user->name,
                'email' => $user->email,
            ],
            'success_redirect_url' => "https://app.dailyhotels.id/premium/done?uid=" . $user->id,
        ]);

        $savePremium = UserPremium::create([
            'user_id' => $user->id,
            'external_id' => $externalID,
            'active_until' => $newExpiration,
            'month_quantity' => $monthQuantity,
            'amount' => $amount,
            'status' => 'pending',
            'payment_link' => $createInvoice['invoice_url']
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Pengaturan baru berhasil disimpan",
            'invoice' => $createInvoice,
        ]);
    }
    public function getConfig($key) {
        $cfg = config('inhotel')[$key];
        return response()->json($cfg);
    }
    public function order(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->first();
        $filter = [['user_id', $user->id]];
        $query = Order::where($filter);
        $search = $request->search;
        
        $orders = $query->with(['product.images', 'visitor']);
        if ($search != "") {
            $orders = $orders->whereHas('visitor', function ($query) use ($search) {
                $query->where('name', 'LIKE', '%'.$search.'%');
            });
        }
        $orders = $orders->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
    public function withdraw(Request $request) {
        $user = ControllersUserController::getByToken($request->token)->first();
        $bank = BankController::get([['id', $request->bank_id]])->first();
        $amount = 0;

        $datas = Payment::where([
            ['user_id', $user->id],
            ['has_withdrawn', 0],
            ['status', 1]
        ]);

        $amount = $datas->get()->sum('grand_total');
        $datas->update([
            'has_withdrawn' => 1,
        ]);

        $referenceID = "DHID_".$user->id;
        $idempotencyKey = Str::random(12);
        $secretKey = env('XENDIT_MODE') == 'sandbox' ? env('XENDIT_SECRET_KEY_SANDBOX') : env('XENDIT_SECRET_KEY');

        $httpRequest = Http::withBasicAuth($secretKey, '')
        ->withHeaders([
            'Idempotency-key' => $idempotencyKey
        ])
        ->post('https://api.xendit.co/v2/payouts', [
            'reference_id' => $referenceID,
            'channel_code' => "ID_" . $bank->bank_code,
            'channel_properties' => [
                'account_holder_name' => $bank->account_name,
                'account_number' => $bank->account_number
            ],
            'amount' => intval($amount),
            'description' => "Contoh payout",
            'currency' => "IDR"
        ]);
        
        $response = json_decode($httpRequest->body());

        $saveData = UserWithdraw::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'withdraw_id' => $response->id,
            'amount' => $amount,
            'status' => $response->status,
            'eta' => $response->estimated_arrival_time
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function checkWithdraw(Request $request) {
        $data = UserWithdraw::where('id', $request->id);
        $withdraw = $data->first();

        $secretKey = env('XENDIT_MODE') == 'sandbox' ? env('XENDIT_SECRET_KEY_SANDBOX') : env('XENDIT_SECRET_KEY');
        $response = Http::withBasicAuth($secretKey, '')
        ->get('https://api.xendit.co/v2/payouts/' . $withdraw->withdraw_id);

        $res = json_decode($response->body());
        
        if ($withdraw->status != $res->status) {
            $data->update(['status' => $res->status]);
        }
        $withdraw = $data->with(['bank'])->first();

        return response()->json([
            'status' => 200,
            'withdraw' => $withdraw
        ]);
    }
}
