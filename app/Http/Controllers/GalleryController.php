<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Gallery;
        }
        return Gallery::where($filter);
    }
}
