<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public static function get($filter = NULL) {
        if ($filter == NULL) {
            return new Bank;
        }
        return Bank::where($filter);
    }
    public static function create($props) {
        $saveData = Bank::create([
            'user_id' => $props['user_id'],
            'bank_name' => $props['bank_name'],
            'bank_code' => $props['bank_code'],
            'account_name' => $props['account_name'],
            'account_number' => $props['account_number'],
        ]);

        return $saveData;
    }
    public static function update($id, $props) {
        $query = Bank::where('id', $id);
        $update = $query->update($props);
        $bank = $query->first();

        return $bank;
    }
    public static function delete($id)  {
        $query = Bank::where('id', $id);
        $bank = $query->first();

        $query->delete();

        return "ok";
    }
}
