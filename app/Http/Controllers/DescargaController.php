<?php

namespace App\Http\Controllers;

use App\Models\DescargaJob;
use App\Rules\RfcValido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DescargaController
{
    public function create(): View
    {
        return view('descarga.nueva');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'rfc' => ['required', 'string', 'min:12', 'max:13', new RfcValido()],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'tipo' => ['required', 'in:emitidos,recibidos'],
            'signed_token' => ['required', 'string'],
        ]);

        return back()->with('status', __('app.descarga.pending_schema'));
    }

    public function show(DescargaJob $descargaJob): View
    {
        return view('descarga.estado', [
            'descarga' => $descargaJob,
        ]);
    }
}
