<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BannerController as ControllersBannerController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function get(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $banners = ControllersBannerController::get([['user_id', $user->id]])
        ->orderBy('priority', 'DESC')->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil banner",
            'banners' => $banners
        ]);
    }
    public function store(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        
        $save = ControllersBannerController::create([
            'user_id' => $user->id,
            'link' => $request->link,
        ], $request->file('image'));

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan banner"
        ]);
    }
    public function delete(Request $request) {
        $delete = ControllersBannerController::delete($request->id);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus banner"
        ]);
    }
    public function priority(Request $request) {
        $setting = ControllersBannerController::priority($request->id, $request->action);
        
        return response()->json([
            'status' => 200,
        ]);
    }
}
