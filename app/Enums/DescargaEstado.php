<?php

namespace App\Enums;

enum DescargaEstado: string
{
    case Pendiente = 'pendiente';
    case Autenticando = 'autenticando';
    case Solicitada = 'solicitada';
    case Descargando = 'descargando';
    case Procesando = 'procesando';
    case Completada = 'completada';
    case Fallida = 'fallida';
}
