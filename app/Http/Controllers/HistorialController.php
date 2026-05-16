<?php

namespace App\Http\Controllers;

use App\Support\MembershipAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HistorialController
{
    public function __invoke(Request $request): View
    {
        abort_unless(MembershipAccess::canAccessFullHistory($request->user()), 403);

        $history = collect();

        if (Schema::hasTable('descarga_jobs')) {
            $history = DB::table('descarga_jobs')
                ->leftJoin('rfcs', 'rfcs.id', '=', 'descarga_jobs.rfc_id')
                ->where('descarga_jobs.user_id', $request->user()->id)
                ->select([
                    'descarga_jobs.id',
                    'descarga_jobs.tipo',
                    'descarga_jobs.estado',
                    'descarga_jobs.fecha_inicio',
                    'descarga_jobs.fecha_fin',
                    'descarga_jobs.solicitud_id',
                    'descarga_jobs.total_cfdi',
                    'descarga_jobs.created_at',
                    'rfcs.rfc',
                    'rfcs.razon_social',
                ])
                ->orderByDesc('descarga_jobs.created_at')
                ->paginate(25);
        }

        return view('historial.index', [
            'history' => $history,
        ]);
    }
}
