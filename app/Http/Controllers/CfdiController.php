<?php

namespace App\Http\Controllers;

use App\Models\Cfdi;
use Illuminate\Contracts\View\View;

class CfdiController
{
    public function index(): View
    {
        return view('dashboard.cfdi.lista', [
            'cfdis' => collect(),
        ]);
    }

    public function show(Cfdi $cfdi): View
    {
        return view('dashboard.cfdi.detalle', [
            'cfdi' => $cfdi,
        ]);
    }
}
