<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Models\Visitor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController as ControllersVisitorController;
use App\Mail\VisitorCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
    public function cart(Request $request) {
        $carts = OrderController::get([
            ['visitor_id', $request->visitor_id],
            ['user_id', $request->user_id],
            ['is_placed', 0]
        ])
        ->with(['product.images'])
        ->get();

        return response()->json([
            'carts' => $carts
        ]);
    }
    public function cartQuantity(Request $request) {
        $data = OrderController::get([['id', $request->id]]);
        $cart = $data->with('product')->first();
        $isDeleting = false;

        if ($request->action == "increase") {
            $newQuantity = $cart->quantity + 1;
        } else {
            if ($cart->quantity == 1) {
                $deleteData = $data->delete();
                $isDeleting = true;
            } else {
                $newQuantity = $cart->quantity - 1;
            }
        }
        
        if (!$isDeleting) {
            $newPrice = $newQuantity * $cart->product->price;
            $updateCart = $data->update([
                'quantity' => $newQuantity,
                'total' => $newPrice,
            ]);
        }

        return response()->json(['ok']);
    }
    public function checkout(Request $request) {
        $cartQuery = OrderController::get([
            ['visitor_id', $request->visitor_id],
            ['user_id', $request->user_id],
            ['is_placed', 0]
        ]);
        
        $carts = $cartQuery->with(['user', 'visitor'])->get();
        $visitor = $carts[0]->visitor;
        $user = $carts[0]->user;

        $total = $carts->sum('total');
        $invoice = ApiOrderController::createPayment([
            'visitor' => $visitor,
            'user' => $user,
            'total' => $total,
            'redirect_url' => "https://app.dailyhotels.id/payment/done?uid=".$user->id."&vid=" . $visitor->id
        ]);

        $checkingOut = $cartQuery->update([
            'is_placed' => 1,
            'payment_id' => $invoice->id,
        ]);

        // update booking date
        foreach ($request->bookdate as $i => $dt) {
            OrderController::get([
                ['id', $carts[$i]->id]
            ])->update(['book_date' => $dt]);
        }

        return response()->json([
            'payment' => $invoice,
        ]);

        // Mail::to($visitor->email)->send(new VisitorCheckout([
        //     'visitor' => $visitor,
        //     'carts' => $carts
        // ]));
    }
}
