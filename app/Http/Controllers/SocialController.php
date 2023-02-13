<?php

namespace App\Http\Controllers;

use App\Models\Social;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Social;
        }
        return Social::where($filter);
    }
    public static function create($props) {
        $saveData = Social::create([
            'user_id' => $props['user_id'],
            'type' => $props['type'],
            'url' => $props['url'],
        ]);

        return $saveData;
    }
    public static function update($id, $props) {
        $query = Social::where('id', $id);
        $update = $query->update($id);

        return $query->first();
    }
    public static function delete($id) {
        $query = Social::where('id', $id);
        $delete = $query->delete();

        return $query->first();
    }
}
