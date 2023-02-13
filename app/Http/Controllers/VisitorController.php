<?php

namespace App\Http\Controllers;

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
}
