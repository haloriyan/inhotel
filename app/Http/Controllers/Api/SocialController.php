<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SocialController as ControllersSocialController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function store(Request $request) {
        $user = UserController::getByToken($request->token)->first();

        $store = ControllersSocialController::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'url' => $request->url
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan akun sosial media"
        ]);
    }
    public function update(Request $request) {
        $update = ControllersSocialController::update($request->id, [
            'url' => $request->url,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah URL sosial media"
        ]);
    }
    public function delete(Request $request) {
        $delete = ControllersSocialController::delete($request->id);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus sosial media"
        ]);
    }
}
