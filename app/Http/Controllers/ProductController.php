<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Product;
        }
        return Product::where($filter);
    }
    public static function create($props) {
        $saveData = Product::create([
            'user_id' => $props['user_id'],
            'category' => $props['category'],
            'name' => $props['name'],
            'description' => $props['description'],
            'price' => $props['price'],
            'quantity_per_day' => $props['quantity'],
            'visibility' => 1,
        ]);

        return $saveData;
    }
    public static function update($id, $props) {
        $query = Product::where('id', $id);
        $update = $query->update($id);

        return $query->first();
    }
    public static function delete($id) {
        $query = Product::where('id', $id);
        $delete = $query->delete();

        return $query->first();
    }
    public static function saveImage($productID, $image) {
        $imageFileName = $image->getClientOriginalName();
        $image->storeAs('public/product_images', $imageFileName);
        $saveData = ProductImage::create([
            'product_id' => $productID,
            'filename' => $imageFileName,
            'priority' => 1,
        ]);

        return $saveData;
    }
    public static function deleteImage($productID, $filename) {
        $query = ProductImage::where([
            ['product_id', $productID],
            ['filename', $filename]
        ]);
        $image = $query->first();

        $deleteData = $query->delete();
        $deleteImage = Storage::delete('public/product_images/' . $image->filename);

        return true;
    }
}
