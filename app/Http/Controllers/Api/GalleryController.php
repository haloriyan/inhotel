<?php

namespace App\Http\Controllers\Api;

use Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function images(Request $request) {
        // $gallery = Gallery::where('id', $request->id)->with('images')->first();
        $images = GalleryImage::where('gallery_id', $request->id)
        ->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'images' => $images
        ]);
    }
    public function all(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $galleries = Gallery::where('user_id', $user->id)->with('images')->get();

        return response()->json([
            'galleries' => $galleries
        ]);
    }
    public function create(Request $request) {
        $user = UserController::getByToken($request->token)->first();

        $saveData = Gallery::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'style' => 'grid',
            'item_per_row' => 3
        ]);

        return response()->json([
            'gallery' => $saveData,
        ]);
    }
    public function upload(Request $request) {
        $gallery = Gallery::where('id', $request->id)->with('user')->first();
        $user = $gallery->user;
        
        $image = $request->file('image');
        $imageFileName = $image->getClientOriginalName();
        $newFileName = Str::slug($user->id)."_".$imageFileName;

        $saveImage = GalleryImage::create([
            'gallery_id' => $gallery->id,
            'filename' => $newFileName,
            'priority' => 0,
        ]);

        $image->storeAs('public/gallery_images/' . $user->id, $newFileName);

        return response()->json(['message' => "ok"]);
    }
    public function remove(Request $request) {
        $query = GalleryImage::where('id', $request->id);
        $image = $query->with('gallery.user')->first();
        $user = $image->gallery->user;

        $query->delete();
        Storage::delete('public/gallery_images/' . $user->id . '/' . $image->filename);

        return response()->json(['message' => "ok"]);
    }
}
