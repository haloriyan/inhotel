<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController as ControllersProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function get($id) {
        $product = ControllersProductController::get([['id', $id]])->with('images')->first();
        return response()->json([
            'product' => $product,
        ]);
    }
    public function visibility(Request $request) {
        $product = ControllersProductController::get([['id', $request->id]])->update([
            'visibility' => $request->visibility
        ]);
        return response()->json(['message' => "ok"]);
    }
    public function store(Request $request) {
        $user = UserController::getByToken($request->token)->first();

        $saveData = ControllersProductController::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'quantity' => $request->quantity,
        ]);

        foreach ($request->file('images') as $image) {
            $saveImage = ControllersProductController::saveImage($saveData->id, $image);
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan produk"
        ]);
    }
    public function delete(Request $request) {
        $user = UserController::getByToken($request->token)->first();
        $query = ControllersProductController::get([['id', $request->id]]);
        $product = $query->with('images')->first();

        if ($product->user_id == $user->id) {
            $query->delete();
            foreach ($product->images as $image) {
                $deleteImage = Storage::delete('public/product_images/' . $image->filename);
            }
        } else {
            return response()->json(['message' => "error"]);
        }

        return response()->json(['message' => "ok"]);
    }
}
