<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VisitorExport implements FromView, ShouldAutoSize
{
    public $datas = null;

    public function __construct($props) {
        $this->datas = $props['datas'];
    }

    public function view(): View {
        return view('exports.visitor', [
            'datas' => $this->datas
        ]);
    }
}
