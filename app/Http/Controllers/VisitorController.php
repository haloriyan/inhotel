<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Visitor;
        }
        return Visitor::where($filter);
    }
    public static function check($props) {
        return Visitor::where([
            ['name', $props['name']],
            ['email', $props['email']],
        ])
        ->orWhere([
            ['name', $props['name']],
            ['phone', $props['phone']],
        ])
        ->first();
    }
    public function paymentDone(Request $request) {
        $userID = $request->uid;
        $user = UserController::getByID($userID)->first();
        $visitorID = $request->vid;

        if ($visitorID == "") {
            // subscription
            $message = "Terima kasih telah mengupgrade akun Anda menjadi Pro";
        } else {
            // payment
            $latestPayment = Payment::where('visitor_id', $visitorID)
            ->orderBy('created_at', 'DESC')->take(1)->first();
            $message = "Terima kasih telah melakukan pembayaran untuk invoice #" . $latestPayment->invoice_number;
        }

        return view('paymentDone', [
            'accent_color' => $user->accent_color,
            'message' => $message
        ]);
    }
}
