<?php

namespace App\Http\Controllers\Api;

use App\Models\Visitor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController as ControllersVisitorController;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function login(Request $request) {
        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $check = ControllersVisitorController::check($payload);

        if ($check == "") {
            $register = Visitor::create([
                'user_id' => $request->user_id,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'],
            ]);
        } else {
            return response()->json([
                'visitor' => $check
            ]);
        }

        return response()->json([
            'visitor' => $register
        ]);
    }
    public function load(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $filter = [['user_id', $user->id]];
        if ($request->search != "") {
            array_push($filter, ['name', 'LIKE', '%'.$request->search.'%']);
        }
        $visitors = ControllersVisitorController::get($filter)->orderBy('created_at', 'DESC')->paginate(50);

        return response()->json([
            'status' => 200,
            'visitors' => $visitors,
        ]);
    }
}
