# PASO 4 — PLANES Y LÍMITES DE NEGOCIO
## ContadorMx · xml.contadormx.net

---

## PLANES Y LÍMITES DE NEGOCIO

```php
// config/planes.php
return [
    'gratis' => [
        'max_rfcs'            => 1,
        'descarga_cfdi'       => true,
        'exportar_excel'      => true,  // metadata básica
        'dashboard_fiscal'    => false,
        'validacion_efos'     => false,
        'conversion_pdf'      => false,
        'constancia_fiscal'   => false,
        'opinion_cumplimiento'=> false,
        'alertas_cancelacion' => false,
        'almacenamiento_xml'  => false,  // solo metadata en DB, no guarda XML
        'conciliacion_reps'   => false,
        'reporte_diot'        => false,
        'api_access'          => false,
        'soporte'             => 'ninguno',
        'precio_anual_mxn'    => 0,
    ],
    'pro' => [
        'max_rfcs'            => 999,   // ilimitado
        'descarga_cfdi'       => true,
        'exportar_excel'      => true,  // avanzado con filtros
        'dashboard_fiscal'    => true,  // IVA/ISR por período
        'validacion_efos'     => true,
        'conversion_pdf'      => true,
        'constancia_fiscal'   => true,
        'opinion_cumplimiento'=> true,
        'alertas_cancelacion' => true,
        'almacenamiento_xml'  => true,  // XML cifrados en storage
        'conciliacion_reps'   => false,
        'reporte_diot'        => false,
        'api_access'          => false,
        'soporte'             => 'email',
        'precio_anual_mxn'    => 299,
    ],
    'despacho' => [
        'max_rfcs'            => 999,
        'descarga_cfdi'       => true,
        'exportar_excel'      => true,
        'dashboard_fiscal'    => true,
        'validacion_efos'     => true,
        'conversion_pdf'      => true,
        'constancia_fiscal'   => true,
        'opinion_cumplimiento'=> true,
        'alertas_cancelacion' => true,
        'almacenamiento_xml'  => true,
        'conciliacion_reps'   => true,
        'reporte_diot'        => true,
        'descarga_lote'       => true,  // todos los RFC con un clic
        'api_access'          => true,
        'soporte'             => 'prioritario',
        'precio_anual_mxn'    => 799,
    ],
];
```
