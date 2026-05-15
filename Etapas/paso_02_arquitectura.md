# PASO 2 — ARQUITECTURA GENERAL DEL SISTEMA
## ContadorMx · xml.contadormx.net

---

## ARQUITECTURA GENERAL DEL SISTEMA

```
xml.contadormx.net (dominio principal)
│
├── /public_html/contadormx/          ← raíz del proyecto Laravel
│   ├── public/                       ← webroot del dominio (único directorio público)
│   │   ├── index.php
│   │   ├── js/
│   │   │   ├── efirma.js             ← WebCrypto: firma en browser, NUNCA sube .key
│   │   │   ├── descarga.js           ← polling de estado de jobs
│   │   │   └── dashboard.js          ← Chart.js + filtros Alpine.js
│   │   └── css/app.css
│   │
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Auth/                 ← registro, login, verificación email
│   │   │   ├── DescargaController.php
│   │   │   ├── CfdiController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── RfcController.php
│   │   │   ├── SuscripcionController.php
│   │   │   └── WebhookController.php ← Conekta + Stripe webhooks
│   │   │
│   │   ├── Jobs/
│   │   │   ├── ProcesarDescargaSat.php   ← job async principal
│   │   │   ├── ValidarEstatusCfdi.php    ← verifica cancelaciones
│   │   │   └── EnviarAlertaCancelacion.php
│   │   │
│   │   ├── Services/
│   │   │   ├── SatWebService.php         ← cliente SOAP del SAT (núcleo)
│   │   │   ├── CfdiParser.php            ← parsea XML → array → DB
│   │   │   ├── EfirmaValidator.php       ← valida token firmado recibido
│   │   │   ├── CfdiStorage.php           ← cifra/descifra + guarda archivos
│   │   │   ├── ReporteFiscal.php         ← genera Excel/PDF de reportes
│   │   │   └── PacService.php            ← genera CFDI del cobro (Facturama)
│   │   │
│   │   └── Models/
│   │       ├── User.php
│   │       ├── Rfc.php
│   │       ├── Cfdi.php
│   │       ├── CfdiConcepto.php
│   │       ├── DescargaJob.php
│   │       └── Suscripcion.php
│   │
│   ├── resources/views/
│   │   ├── layouts/app.blade.php         ← layout principal Bootstrap 5
│   │   ├── auth/                         ← login, registro, forgot password
│   │   ├── dashboard/
│   │   │   ├── index.blade.php           ← resumen IVA/ISR + gráficas
│   │   │   └── cfdi/lista.blade.php      ← tabla CFDI con filtros
│   │   ├── descarga/
│   │   │   ├── nueva.blade.php           ← subida e.firma (WebCrypto JS)
│   │   │   └── estado.blade.php          ← polling estado del job
│   │   └── suscripcion/planes.blade.php
│   │
│   ├── database/migrations/              ← esquema completo MySQL
│   ├── routes/web.php                    ← todas las rutas
│   ├── routes/api.php                    ← endpoints JSON para Alpine.js
│   └── storage/app/private/             ← XML/PDF cifrados (fuera de webroot)
│       └── {user_id}/{rfc}/             ← por usuario y RFC
```
