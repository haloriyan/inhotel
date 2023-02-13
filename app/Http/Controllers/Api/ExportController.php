<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

use App\Exports\VisitorExport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;

class ExportController extends Controller
{
    public function visitor(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $now = Carbon::now();
        $filename = $user->name."_Visitor_Report_";

        $filter = [['user_id', $user->id]];
        $query = VisitorController::get($filter);
        if ($request->period == "month") {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $query = $query->whereBetween('created_at', [$startDate, $endDate]);
            $filename .= $now->isoFormat('MMMM')."_".$now->isoFormat('Y');
        } else if ($request->period == "year") {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $query = $query->whereBetween('created_at', [$startDate, $endDate]);
            $filename .= $now->isoFormat('Y');
        }

        $filename .= ".xlsx";
        $visitors = $query->get();

        $storeFile = Excel::store(
            new VisitorExport(['datas' => $visitors]),
            $filename, 'export'
        );

        return response()->json([
            'status' => 200,
            'link' => asset('storage/export/'.$filename)
        ]);
    }
}
