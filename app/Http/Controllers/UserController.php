<?php

namespace App\Http\Controllers;

use Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function getByToken($token) {
        return User::where('token', $token);
    }
    public static function getByID($id) {
        return User::where('id', $id);
    }

    public function login(Request $request) {
        $user = null;
        $status = 200;
        $message = "Berhasil login";
        $email = $request->email;

        $query = User::where('email', $request->email);
        $user = $query->first();

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

        return response()->json([
            'status' => $status,
            'message' => $message,
            'user' => $user
        ]);
    }

    public function homepage($username, Request $request, $category = null) {
        $user = User::where('username', $username)
        ->with(['socials'])
        ->first();
        
        $filter = [
            ['user_id', $user->id]
        ];
        if ($category != null) {
            array_push($filter, ['category', $category]);
        }
        if ($request->q != "") {
            array_push($filter, ['name', 'LIKE', '%'.$request->q.'%']);
        }

        $products = ProductController::get($filter)->with('images')->get();
        $galleries = GalleryController::get([['user_id', $user->id]])->with('images')->get();

        return view('homepage', [
            'user' => $user,
            'accent_color' => $user->accent_color,
            'category' => $category,
            'request' => $request,
            'products' => $products,
            'galleries' => $galleries,
        ]);
    }
    public function product($username, $id) {
        $user = User::where('username', $username)->first();
        $product = ProductController::get([['id', $id]])->with('images')->first();

        return view('product', [
            'user' => $user,
            'accent_color' => $user->accent_color,
            'product' => $product,
        ]);
    }
    public function cart($username) {
        $user = User::where('username', $username)->first();
        $carts = OrderController::get([
            ['user_id', $user->id],
            ['is_placed', 0]
        ])
        ->with(['product'])
        ->get();
        
        return view('cart', [
            'user' => $user,
            'accent_color' => $user->accent_color,
            'carts' => $carts,
        ]);
    }
}
