<?php

namespace App\Enums;

enum CfdiEstatus: string
{
    case Vigente = 'vigente';
    case Cancelado = 'cancelado';
    case Desconocido = 'desconocido';
}
