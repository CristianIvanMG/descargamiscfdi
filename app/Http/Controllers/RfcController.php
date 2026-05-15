<?php

namespace App\Http\Controllers;

use App\Rules\RfcValido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RfcController
{
    public function index(): View
    {
        return view('rfc.index', [
            'rfcs' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'rfc' => ['required', 'string', 'min:12', 'max:13', new RfcValido()],
            'razon_social' => ['required', 'string', 'max:255'],
        ]);

        return back()->with('status', __('app.rfc.pending_schema'));
    }

    public function destroy(string $rfc): RedirectResponse
    {
        return back()->with('status', __('app.rfc.pending_schema'));
    }
}
