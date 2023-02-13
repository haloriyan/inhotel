<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\VisitorOrder as Order;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function add(Request $request) {
        $userID = $request->user_id;
        $visitorID = $request->visitor_id;
        $productID = $request->product_id;

        $queryToCheck = Order::where([
            ['visitor_id', $visitorID],
            ['user_id', $userID],
            ['product_id', $productID],
        ]);

        $product = ProductController::get([['id', $productID]])->first();

        if ($queryToCheck->get()->count() == 0) {
            $saveData = Order::create([
                'user_id' => $userID,
                'visitor_id' => $visitorID,
                'product_id' => $productID,
                'quantity' => 1,
                'total' => $product->price,
                'is_placed' => 0,
                'has_used' => 0,
            ]);

            $message = "Berhasil menambahkan produk ke keranjang";
        } else {
            $order = $queryToCheck->first();
            $newQuantity = $order->quantity + 1;
            $newTotal = $product->price * $newQuantity;
            $queryToCheck->update([
                'quantity' => $newQuantity,
                'total' => $newTotal
            ]);
            $message = "Berhasil menambahkan jumlah item di keranjang";
        }

        return response()->json([
            'message' => $message
        ]);
    }

    public function paymentCallback(Request $request) {
        return response()->json([
            'external_id' => $request->external_id
        ]);
    }
}
