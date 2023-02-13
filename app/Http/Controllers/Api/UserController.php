<?php

namespace App\Http\Controllers\Api;

use Str;
use Hash;
use App\Models\User;
use App\Models\UserPremium;
use App\Models\VisitorOrder as Order;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\UserController as ControllersUserController;
use Carbon\Carbon;
use Xendit\Xendit as Xendit;
use Illuminate\Http\Request;
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
    public function forgetPassword(Request $request) {
        $user = User::where('email', $request->email)->first();
        if ($user == "") {
            return response()->json([
                'status' => 500,
                'message' => "Kami tidak dapat menemukan akun Anda"
            ]);
        } else {
            OtpController::send($user, 'password');
        }
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

        $externalID = "DHID_".Str::random(24);
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
            'success_redirect_url' => "https://app.dailyhotels.id/premium/done",
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
        $orders = Order::where('user_id', $user->id)
        ->with(['product.images', 'visitor'])
        ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
