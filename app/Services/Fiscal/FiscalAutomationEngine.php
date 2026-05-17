<?php

namespace App\Services\Fiscal;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FiscalAutomationEngine
{
    public function process(array $input): array
    {
        $emitidos = collect($input['cfdi_emitidos'] ?? []);
        $recibidos = collect($input['cfdi_recibidos'] ?? []);
        $bancos = collect($input['movimientos_bancarios'] ?? []);
        $regimen = (string) ($input['regimen'] ?? '');
        $periodo = (string) ($input['periodo'] ?? '');
        $extra = (array) ($input['datos_extra'] ?? []);

        $clasificacion = $this->classify($emitidos, $recibidos);
        $impuestos = $this->calculateTaxes($clasificacion, $regimen, $extra);
        $inconsistencias = $this->validateInconsistencies($clasificacion, $impuestos, $bancos, $extra);
        $simulaciones = $this->simulate($clasificacion, $regimen, $extra);
        $alertas = $this->alerts($clasificacion, $impuestos, $inconsistencias, $regimen);
        $declaracion = $this->declarationPayload($clasificacion, $impuestos, $periodo, $regimen);
        $validaciones = $this->finalValidations($input, $clasificacion, $impuestos, $regimen, $extra);
        $resumen = $this->summary($clasificacion, $impuestos, $inconsistencias, $validaciones);

        return [
            'clasificacion' => $clasificacion,
            'impuestos' => $impuestos,
            'inconsistencias' => $inconsistencias,
            'simulaciones' => $simulaciones,
            'alertas' => $alertas,
            'declaracion_prellenada' => $declaracion,
            'validaciones' => $validaciones,
            'resumen' => $resumen,
        ];
    }

    private function classify(Collection $emitidos, Collection $recibidos): array
    {
        $seen = [];
        $rows = [];

        foreach ($emitidos as $cfdi) {
            $uuid = $this->uuid($cfdi);
            $tipo = $this->cfdiType($cfdi);
            $alerts = $this->cfdiStructuralAlerts($cfdi, $seen);
            $seen[$uuid] = true;

            $classification = match ($tipo) {
                'I' => 'ingreso_acumulable',
                'E' => 'ingreso_disminucion',
                'P' => 'pago_informativo',
                'N' => 'nomina_informativa',
                default => 'revision_manual',
            };

            $rows[] = $this->classifiedRow($cfdi, $classification, 'emitido', $alerts);
        }

        foreach ($recibidos as $cfdi) {
            $uuid = $this->uuid($cfdi);
            $tipo = $this->cfdiType($cfdi);
            $alerts = $this->cfdiStructuralAlerts($cfdi, $seen);
            $seen[$uuid] = true;

            $classification = match (true) {
                $tipo === 'I' && $this->hasDeductibleUse($cfdi) => 'gasto_deducible',
                $tipo === 'I' => 'gasto_no_deducible',
                $tipo === 'E' => 'gasto_disminucion',
                $tipo === 'P' => 'pago_informativo',
                $tipo === 'N' => 'nomina_informativa',
                default => 'revision_manual',
            };

            $rows[] = $this->classifiedRow($cfdi, $classification, 'recibido', $alerts);
        }

        return $rows;
    }

    private function calculateTaxes(array $clasificacion, string $regimen, array $extra): array
    {
        $ingresos = $this->sumBy($clasificacion, 'ingreso_acumulable', 'total');
        $deducciones = $this->sumBy($clasificacion, 'gasto_deducible', 'subtotal');
        $base = max(0, $ingresos - $deducciones);
        $retencionesIsr = $this->sumField($clasificacion, 'retencion_isr');
        $ivaTrasladado = $this->sumBy($clasificacion, 'ingreso_acumulable', 'iva');
        $ivaAcreditable = $this->sumBy($clasificacion, 'gasto_deducible', 'iva');
        $retencionesIva = $this->sumField($clasificacion, 'retencion_iva');
        $isrRate = $this->isrRate($regimen, $extra);
        $isrCausado = round($base * $isrRate, 2);
        $isrPagar = max(0, round($isrCausado - $retencionesIsr, 2));
        $ivaNeto = max(0, round($ivaTrasladado - $ivaAcreditable - $retencionesIva, 2));

        return [
            'ISR' => [
                'ingresos_acumulables' => round($ingresos, 2),
                'deducciones_autorizadas' => round($deducciones, 2),
                'base_gravable' => round($base, 2),
                'tasa_aplicada' => $isrRate,
                'isr_causado' => $isrCausado,
                'retenciones' => round($retencionesIsr, 2),
                'isr_a_pagar' => $isrPagar,
                'trazabilidad' => [
                    'base_gravable = ingresos_acumulables - deducciones_autorizadas',
                    'isr_a_pagar = max(0, isr_causado - retenciones)',
                ],
            ],
            'IVA' => [
                'iva_trasladado' => round($ivaTrasladado, 2),
                'iva_acreditable' => round($ivaAcreditable, 2),
                'retenciones_iva' => round($retencionesIva, 2),
                'iva_neto' => $ivaNeto,
                'trazabilidad' => [
                    'iva_neto = max(0, iva_trasladado - iva_acreditable - retenciones_iva)',
                ],
            ],
        ];
    }

    private function validateInconsistencies(array $clasificacion, array $impuestos, Collection $bancos, array $extra): array
    {
        $issues = [];
        $ingresoBancario = (float) $bancos->sum(fn ($m) => (float) Arr::get($m, 'monto', 0));
        $ingresoCfdi = (float) Arr::get($impuestos, 'ISR.ingresos_acumulables', 0);

        if ($ingresoBancario > $ingresoCfdi + 1) {
            $issues[] = [
                'tipo' => 'warning',
                'descripcion' => 'Los ingresos bancarios superan los ingresos CFDI del periodo.',
                'impacto' => 'ambos',
                'nivel_riesgo' => 'alto',
            ];
        }

        foreach ($clasificacion as $row) {
            foreach ($row['alertas'] as $alert) {
                $issues[] = $alert;
            }
        }

        if ((float) Arr::get($impuestos, 'IVA.iva_acreditable', 0) > 0 && empty($extra['validar_metodo_pago'])) {
            $issues[] = [
                'tipo' => 'warning',
                'descripcion' => 'Hay IVA acreditable; valida metodo de pago, uso CFDI y requisitos fiscales antes de declarar.',
                'impacto' => 'IVA',
                'nivel_riesgo' => 'medio',
            ];
        }

        return $issues;
    }

    private function simulate(array $clasificacion, string $regimen, array $extra): array
    {
        $actual = $this->calculateTaxes($clasificacion, $regimen, $extra);
        $optimizadoRows = array_map(function (array $row): array {
            if ($row['clasificacion'] === 'gasto_no_deducible' && $row['origen'] === 'recibido') {
                $row['clasificacion'] = 'gasto_deducible';
                $row['supuesto'] = 'Solo si se valida uso CFDI, metodo de pago y requisitos fiscales.';
            }

            return $row;
        }, $clasificacion);
        $conservadorRows = array_map(function (array $row): array {
            if ($row['clasificacion'] === 'gasto_deducible' && $row['nivel_confianza'] !== 'alto') {
                $row['clasificacion'] = 'gasto_no_deducible';
            }

            return $row;
        }, $clasificacion);

        return [
            ['escenario' => 'actual', 'impuestos' => $actual, 'total_a_pagar' => $this->totalPayable($actual)],
            ['escenario' => 'optimizado', 'impuestos' => $this->calculateTaxes($optimizadoRows, $regimen, $extra), 'total_a_pagar' => $this->totalPayable($this->calculateTaxes($optimizadoRows, $regimen, $extra))],
            ['escenario' => 'conservador', 'impuestos' => $this->calculateTaxes($conservadorRows, $regimen, $extra), 'total_a_pagar' => $this->totalPayable($this->calculateTaxes($conservadorRows, $regimen, $extra))],
        ];
    }

    private function alerts(array $clasificacion, array $impuestos, array $inconsistencias, string $regimen): array
    {
        $alerts = [];

        if (count($inconsistencias) > 0) {
            $alerts[] = [
                'mensaje' => 'Existen inconsistencias que conviene revisar antes de presentar la declaracion.',
                'tipo' => 'riesgo',
                'accion_recomendada' => 'Revisar UUID duplicados, ingresos bancarios y requisitos de deducibilidad.',
            ];
        }

        if ($this->sumBy($clasificacion, 'gasto_no_deducible', 'total') > 0) {
            $alerts[] = [
                'mensaje' => 'Hay gastos no deducibles que podrian requerir correccion documental.',
                'tipo' => 'ahorro',
                'accion_recomendada' => 'Solicitar CFDI corregido o validar uso CFDI/metodo de pago.',
            ];
        }

        $alerts[] = [
            'mensaje' => 'Regimen procesado: '.$regimen.'. Mantener papeles de trabajo por periodo.',
            'tipo' => 'informativo',
            'accion_recomendada' => 'Conservar XML, acuses y soporte bancario del mes.',
        ];

        return $alerts;
    }

    private function declarationPayload(array $clasificacion, array $impuestos, string $periodo, string $regimen): array
    {
        return [
            'periodo' => $periodo,
            'regimen' => $regimen,
            'ingresos_acumulables' => Arr::get($impuestos, 'ISR.ingresos_acumulables', 0),
            'deducciones' => Arr::get($impuestos, 'ISR.deducciones_autorizadas', 0),
            'iva_trasladado' => Arr::get($impuestos, 'IVA.iva_trasladado', 0),
            'iva_acreditable' => Arr::get($impuestos, 'IVA.iva_acreditable', 0),
            'isr_causado' => Arr::get($impuestos, 'ISR.isr_causado', 0),
            'retenciones_isr' => Arr::get($impuestos, 'ISR.retenciones', 0),
            'retenciones_iva' => Arr::get($impuestos, 'IVA.retenciones_iva', 0),
            'isr_a_pagar' => Arr::get($impuestos, 'ISR.isr_a_pagar', 0),
            'iva_a_pagar' => Arr::get($impuestos, 'IVA.iva_neto', 0),
            'cfdi_relacionados' => array_values(array_filter(array_map(fn ($row) => $row['uuid'] ?? null, $clasificacion))),
        ];
    }

    private function finalValidations(array $input, array $clasificacion, array $impuestos, string $regimen, array $extra): array
    {
        $validations = [];

        foreach (['cfdi_emitidos', 'cfdi_recibidos', 'movimientos_bancarios', 'regimen', 'periodo'] as $key) {
            if (! array_key_exists($key, $input)) {
                $validations[] = ['tipo' => 'warning', 'descripcion' => 'Falta input: '.$key, 'impacto' => 'ambos', 'nivel_riesgo' => 'medio'];
            }
        }

        if ($this->isrRate($regimen, $extra) <= 0) {
            $validations[] = ['tipo' => 'warning', 'descripcion' => 'No se proporciono tasa ISR en datos_extra; ISR queda en cero por supuesto conservador.', 'impacto' => 'ISR', 'nivel_riesgo' => 'alto'];
        }

        if (Arr::get($impuestos, 'ISR.base_gravable', 0) < 0 || Arr::get($impuestos, 'IVA.iva_neto', 0) < 0) {
            $validations[] = ['tipo' => 'error', 'descripcion' => 'La consistencia matematica produjo valores negativos no permitidos.', 'impacto' => 'ambos', 'nivel_riesgo' => 'alto'];
        }

        if (count($clasificacion) === 0) {
            $validations[] = ['tipo' => 'warning', 'descripcion' => 'No hay CFDI para procesar en el periodo.', 'impacto' => 'ambos', 'nivel_riesgo' => 'medio'];
        }

        return $validations;
    }

    private function summary(array $clasificacion, array $impuestos, array $inconsistencias, array $validaciones): array
    {
        $riskPenalty = (count($inconsistencias) * 10) + (count($validaciones) * 8);

        return [
            'ingresos_totales' => Arr::get($impuestos, 'ISR.ingresos_acumulables', 0),
            'gastos_deducibles' => Arr::get($impuestos, 'ISR.deducciones_autorizadas', 0),
            'isr_a_pagar' => Arr::get($impuestos, 'ISR.isr_a_pagar', 0),
            'iva_a_pagar' => Arr::get($impuestos, 'IVA.iva_neto', 0),
            'riesgos_detectados' => count($inconsistencias) + count($validaciones),
            'score_fiscal' => max(0, min(100, 100 - $riskPenalty)),
        ];
    }

    private function classifiedRow(array $cfdi, string $classification, string $origin, array $alerts): array
    {
        return [
            'uuid' => $this->uuid($cfdi),
            'origen' => $origin,
            'tipo_cfdi' => $this->cfdiType($cfdi),
            'clasificacion' => $classification,
            'rfc_emisor' => (string) Arr::get($cfdi, 'rfc_emisor', Arr::get($cfdi, 'emisor.rfc', '')),
            'rfc_receptor' => (string) Arr::get($cfdi, 'rfc_receptor', Arr::get($cfdi, 'receptor.rfc', '')),
            'uso_cfdi' => (string) Arr::get($cfdi, 'uso_cfdi', Arr::get($cfdi, 'receptor.uso_cfdi', '')),
            'subtotal' => $this->money($cfdi, 'subtotal'),
            'iva' => $this->iva($cfdi),
            'total' => $this->money($cfdi, 'total'),
            'retencion_isr' => $this->money($cfdi, 'retencion_isr'),
            'retencion_iva' => $this->money($cfdi, 'retencion_iva'),
            'nivel_confianza' => $alerts === [] ? 'alto' : 'medio',
            'alertas' => $alerts,
        ];
    }

    private function cfdiStructuralAlerts(array $cfdi, array $seen): array
    {
        $alerts = [];
        $uuid = $this->uuid($cfdi);

        if ($uuid !== '' && isset($seen[$uuid])) {
            $alerts[] = ['tipo' => 'error', 'descripcion' => 'UUID duplicado: '.$uuid, 'impacto' => 'ambos', 'nivel_riesgo' => 'alto'];
        }

        $subtotal = $this->money($cfdi, 'subtotal');
        $iva = $this->iva($cfdi);
        $total = $this->money($cfdi, 'total');

        if ($total > 0 && abs(($subtotal + $iva) - $total) > max(1, $total * 0.02)) {
            $alerts[] = ['tipo' => 'warning', 'descripcion' => 'El total CFDI no cuadra contra subtotal + IVA dentro de tolerancia.', 'impacto' => 'ambos', 'nivel_riesgo' => 'medio'];
        }

        if (Str::contains(Str::upper((string) Arr::get($cfdi, 'rfc_emisor', '')), ['XAXX010101000', 'XEXX010101000'])) {
            $alerts[] = ['tipo' => 'warning', 'descripcion' => 'Proveedor o emisor generico detectado.', 'impacto' => 'ISR', 'nivel_riesgo' => 'medio'];
        }

        return $alerts;
    }

    private function hasDeductibleUse(array $cfdi): bool
    {
        $use = Str::upper((string) Arr::get($cfdi, 'uso_cfdi', Arr::get($cfdi, 'receptor.uso_cfdi', '')));

        return in_array($use, ['G01', 'G02', 'G03', 'I01', 'I02', 'I03', 'I04', 'I05', 'I06', 'I07', 'I08'], true);
    }

    private function uuid(array $cfdi): string
    {
        return Str::upper((string) Arr::get($cfdi, 'uuid', Arr::get($cfdi, 'timbre.uuid', '')));
    }

    private function cfdiType(array $cfdi): string
    {
        return Str::upper((string) Arr::get($cfdi, 'tipo', Arr::get($cfdi, 'tipo_comprobante', '')));
    }

    private function money(array $data, string $key): float
    {
        return round((float) Arr::get($data, $key, 0), 2);
    }

    private function iva(array $cfdi): float
    {
        return round((float) Arr::get($cfdi, 'iva', Arr::get($cfdi, 'impuestos.iva_trasladado', 0)), 2);
    }

    private function isrRate(string $regimen, array $extra): float
    {
        return match ($regimen) {
            'RESICO' => (float) Arr::get($extra, 'resico_tasa_isr', 0),
            'ACTIVIDAD_EMPRESARIAL', 'HONORARIOS' => (float) Arr::get($extra, 'tasa_isr_estimada', 0),
            default => (float) Arr::get($extra, 'tasa_isr_estimada', 0),
        };
    }

    private function sumBy(array $rows, string $classification, string $field): float
    {
        return round(collect($rows)->where('clasificacion', $classification)->sum($field), 2);
    }

    private function sumField(array $rows, string $field): float
    {
        return round(collect($rows)->sum($field), 2);
    }

    private function totalPayable(array $taxes): float
    {
        return round((float) Arr::get($taxes, 'ISR.isr_a_pagar', 0) + (float) Arr::get($taxes, 'IVA.iva_neto', 0), 2);
    }
}
