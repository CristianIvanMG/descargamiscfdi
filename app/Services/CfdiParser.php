<?php

namespace App\Services;

use SimpleXMLElement;

class CfdiParser
{
    public function parse(string $xml): array
    {
        $cfdi = new SimpleXMLElement($xml);
        $namespaces = $cfdi->getNamespaces(true);
        $tfd = $cfdi->children($namespaces['cfdi'] ?? null)->Complemento?->children($namespaces['tfd'] ?? null);

        return [
            'version' => (string) ($cfdi['Version'] ?? ''),
            'serie' => (string) ($cfdi['Serie'] ?? ''),
            'folio' => (string) ($cfdi['Folio'] ?? ''),
            'fecha_emision' => (string) ($cfdi['Fecha'] ?? ''),
            'subtotal' => (string) ($cfdi['SubTotal'] ?? '0'),
            'total' => (string) ($cfdi['Total'] ?? '0'),
            'moneda' => (string) ($cfdi['Moneda'] ?? 'MXN'),
            'uuid' => (string) ($tfd?->TimbreFiscalDigital['UUID'] ?? ''),
        ];
    }
}
