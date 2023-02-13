<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
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
}
