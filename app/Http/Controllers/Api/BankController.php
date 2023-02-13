<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BankController as ControllersBankController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function load(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $banks = ControllersBankController::get([['user_id', $user->id]])->get();

        return response()->json([
            'status' => 200,
            'banks' => $banks
        ]);
    }
    public function store(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $saveData = ControllersBankController::create([
            'user_id' => $user->id,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan rekening bank",
            'bank' => $saveData
        ]);
    }
    public function delete(Request $request) {
        $delete = ControllersBankController::delete($request->id);
        
        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus rekening bank",
        ]);
    }
}
