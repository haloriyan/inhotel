<?php

namespace App\Http\Controllers\Api;

use Str;
use App\Models\Payment;
use App\Models\VisitorOrder as Order;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use Xendit\Xendit as Xendit;
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
            ['is_placed', 0]
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
        $externalID = explode("_", $request->external_id);
        if ($externalID[1] == "SUBS") {
            // subscription
        } else {
            // payment
            $data = Payment::where('external_id', implode("_", $externalID));
            $updateData = $data->update([
                'status' => 1,
            ]);
        }

        return response()->json([
            'external_id' => $request->external_id
        ]);
    }
    public static function createPayment($props) {
        $invoiceNumber = rand(111111, 999999);
        $externalID = Str::random(12);

        $externalID = "DHID_INV_".Str::random(12);
        $secretKey = env('XENDIT_MODE') == 'sandbox' ? env('XENDIT_SECRET_KEY_SANDBOX') : env('XENDIT_SECRET_KEY');

        Xendit::setApiKey($secretKey);
        $createInvoice = \Xendit\Invoice::create([
            'external_id' => $externalID,
            'payer_email' => $props['visitor']->email,
            'description' => "Payment for invoice #". $invoiceNumber,
            'amount' => $props['total'],
            'customer' => [
                'given_names' => $props['visitor']->name,
                'email' => $props['visitor']->email,
            ],
            'success_redirect_url' => $props['redirect_url'],
        ]);

        $saveData = Payment::create([
            'user_id' => $props['user']->id,
            'visitor_id' => $props['visitor']->id,
            'invoice_number' => $invoiceNumber,
            'external_id' => $externalID,
            'total' => $props['total'],
            'grand_total' => $props['total'],
            'status' => 0,
            'has_Withdrawn' => 0,
            'payment_link' => $createInvoice['invoice_url']
        ]);
        
        return $saveData;
    }
}
