<?php

namespace App\Http\Controllers;

use App\Models\VisitorOrder as Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Order;
        }
        return Order::where($filter);
    }
}
