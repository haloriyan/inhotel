<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\VisitorOrder as Order;

class ReportController extends Controller
{
    public function summary(Request $request) {
        $now = Carbon::now();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $user = UserController::getByToken($request->token)->first();
        $visitorQuery = VisitorController::get([['user_id', $user->id]]);
        $visitors = $visitorQuery->take(5)->get();

        $counters['visitor'] = $visitorQuery->whereBetween('created_at', [$startDate, $endDate])
        ->get('id');
        $counters['revenue'] = Order::where([
            ['user_id', $user->id],
            // ['is_placed', 1]
        ])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get()->sum('total');

        return response()->json([
            'visitors' => $visitors,
            'counters' => $counters,
        ]);
    }
    public function revenueToWithdraw(Request $request) {
        $user = UserController::getByToken($request->token)->with(['banks', 'withdraws.bank'])->first();
        $amount = 0;

        $datas = Payment::where([
            ['user_id', $user->id],
            ['has_withdrawn', 0],
            ['status', 1]
        ]);
        $amount = $datas->sum('grand_total');

        return response()->json([
            'status' => 200,
            'amount' => $amount,
            'user' => $user,
            // 'revenue' => $revenue
        ]);
    }
    public function payment(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $payments = Payment::where('user_id', $user->id)->with(['visitor'])
        ->paginate(50);

        return response()->json([
            'payments' => $payments,
        ]);
    }
    public function paymentDetail(Request $request) {
        $payment = Payment::where('id', $request->id)->with(['visitor', 'items.product.images'])
        ->first();

        return response()->json([
            'payment' => $payment,
        ]);
    }
}
