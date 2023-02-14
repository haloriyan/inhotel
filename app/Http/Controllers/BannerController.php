<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Banner;
        }
        return Banner::where($filter);
    }
    public static function create($props, $image) {
        $imageFileName = $props['user_id']."_".$image->getClientOriginalName();
        $image->storeAs('public/banner_images', $imageFileName);

        return Banner::create([
            'user_id' => $props['user_id'],
            'filename' => $imageFileName,
            'link' => $props['link'],
            'priority' => 0,
        ]);
    }
    public static function delete($id) {
        $data = Banner::where('id', $id);
        $banner = $data->first();

        $deleteData = $data->delete();
        $deleteFile = Storage::delete('public/banner_images/' . $banner->filename);

        return $banner;
    }
    public static function priority($id, $action) {
        $data = Banner::where('id', $id);

        if ($action == "increase") {
            $data->increment('priority');
        } else {
            $data->decrement('priority');
        }

        return true;
    }
}
