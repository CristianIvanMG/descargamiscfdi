# PASO 10 — CONFIGURACIÓN .env PRODUCCIÓN + CRON JOBS HOSTINGER
## ContadorMx · xml.contadormx.net

---

## CONFIGURACIÓN .env PRODUCCIÓN

```env
APP_NAME="ContadorMx"
APP_ENV=production
APP_KEY=base64:GENERADO_CON_php_artisan_key:generate
APP_DEBUG=false
APP_URL=https://xml.contadormx.net
APP_TIMEZONE=America/Mexico_City

# Base de datos Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_contadormx
DB_USERNAME=u123456789_user
DB_PASSWORD=TU_PASSWORD_SEGURO

# Cola — usa MySQL, sin Redis
QUEUE_CONNECTION=database

# Caché — usa archivos
CACHE_STORE=file

# Sesiones — usa MySQL (consistente con múltiples workers)
SESSION_DRIVER=database
SESSION_LIFETIME=240

# Correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@contadormx.net
MAIL_PASSWORD=TU_PASSWORD_EMAIL
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@contadormx.net
MAIL_FROM_NAME="ContadorMx"

# Pagos México
CONEKTA_KEY=key_XXXXXXXX
CONEKTA_PUBLIC_KEY=key_XXXXXXXX

# Pagos B2B
STRIPE_KEY=pk_live_XXXXXXXX
STRIPE_SECRET=sk_live_XXXXXXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXXXXXX

# PAC para CFDI del cobro
FACTURAMA_USER=XXXXXXXX
FACTURAMA_PASS=XXXXXXXX

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## CRON JOBS EN HOSTINGER

```bash
# hPanel → Cron Jobs → Agregar (usar ruta absoluta de tu servidor)

# 1. Laravel Scheduler — cada minuto (dispara todo lo demás)
* * * * * /usr/bin/php /home/u123456789/public_html/contadormx/artisan schedule:run >> /dev/null 2>&1

# 2. Queue Worker — cada minuto (procesa jobs de descarga pendientes)
* * * * * /usr/bin/php /home/u123456789/public_html/contadormx/artisan queue:work --stop-when-empty --tries=3 --timeout=270 >> /dev/null 2>&1
```

```php
// app/Console/Kernel.php — Schedule de Laravel
protected function schedule(Schedule $schedule): void
{
    // Verificar cancelaciones de CFDI (cada 6 horas)
    $schedule->job(new \App\Jobs\ValidarEstatusCfdi)->everySixHours();
    
    // Limpiar sesiones SAT vencidas de la DB (diario)
    $schedule->command('session:gc')->daily();
    
    // Limpiar jobs completados de la cola (semanal)
    $schedule->command('queue:prune-failed --hours=168')->weekly();
    
    // Recordatorio de renovación (3 días antes de vencer)
    $schedule->job(new \App\Jobs\RecordarRenovacion)->dailyAt('09:00');
}
```
