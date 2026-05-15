# ContadorMx - Paso 1

Base Laravel 11 para validar que Hostinger Business acepta el stack definido para `xml.contadormx.net`.

## Requisitos del servidor

- PHP 8.2 o superior
- Composer 2
- MySQL 8
- Extensiones PHP comunes de Laravel: `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`
- SSH habilitado
- Cron Jobs habilitados

## Instalacion en Hostinger con PHP CLI 8.0

Hostinger puede tener PHP web 8.2 en hPanel, pero PHP CLI 8.0 fijo en SSH. En ese caso no ejecutes `php artisan` por terminal.

Primero sube y descomprime el proyecto en el servidor. Luego entra al directorio donde quedo el archivo `composer.json`.

Para encontrarlo si no recuerdas la ruta:

```bash
find $HOME -maxdepth 5 -name composer.json
```

Despues entra a esa carpeta. Ejemplos comunes en Hostinger:

```bash
cd /home/USUARIO/domains/DOMINIO/public_html
```

o, si el proyecto quedo fuera de `public_html`:

```bash
cd /home/USUARIO/domains/DOMINIO/contadormx
```

Si tu terminal muestra `getcwd: cannot access parent directories`, sal a home y vuelve a entrar a una ruta existente:

```bash
cd ~
pwd
ls
```

Ya dentro de la carpeta que contiene `composer.json`, ejecutar solo Composer para descargar `vendor`:

```bash
composer install --no-dev --optimize-autoloader --ignore-platform-req=php --no-scripts
```

No ejecutes `php artisan` por SSH si `php -v` devuelve 8.0.

Despues abre en el navegador. Si el dominio apunta directo a `public/`, usa:

```text
https://TU-DOMINIO/hostinger-install.php
```

Si Hostinger esta sirviendo el proyecto completo desde `public_html`, primero sube tambien el archivo `.htaccess` de la raiz del proyecto y usa:

```text
https://TU-DOMINIO/hostinger-install.php
```

Sin ese `.htaccess` de raiz, Hostinger lo mostrara temporalmente en:

```text
https://TU-DOMINIO/public/hostinger-install.php
```

El instalador temporal usa PHP web 8.2, crea `.env`, prueba MySQL, ejecuta migraciones y genera cache.

Clave inicial del instalador:

```text
cambia-esta-clave
```

Por seguridad, edita `public/hostinger-install.php` y cambia `$installerPassword` antes de abrirlo en produccion. Cuando termine, elimina ese archivo.

## Documento raiz

Configurar el dominio o subdominio para que apunte a la carpeta:

```text
public/
```

Si hPanel no permite apuntar directamente a `public/`, subir el proyecto fuera de `public_html` y copiar solamente el contenido de `public/` dentro de `public_html`, ajustando las rutas de `public_html/index.php` hacia el directorio real del proyecto.

## Cron base

Cuando el proyecto ya este instalado, agregar este cron en hPanel:

```bash
* * * * * cd /home/USUARIO/domains/xml.contadormx.net && php artisan schedule:run >> /dev/null 2>&1
```

La ruta exacta cambia segun el usuario de Hostinger.

## Seguridad aplicada en fase 1

- El instalador temporal `public/hostinger-install.php` debe eliminarse despues de instalar.
- `.htaccess` raiz bloquea acceso web a `vendor`, `storage`, `config`, `.env`, `composer.json`, backups y logs.
- `public/.htaccess` solo permite ejecutar `index.php` y bloquea PHP suelto dentro de `public`.
- Laravel agrega headers de seguridad: CSP, HSTS, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` y `Permissions-Policy`.
- Cookies de sesion preparadas para `secure`, `http_only`, cifrado y `same_site=lax`.

## Fase 2 - Arquitectura general

La arquitectura base ya incluye:

- Controladores: dashboard, descargas SAT, CFDI, RFC, suscripciones y webhooks.
- API JSON para metricas del dashboard y polling de estado de descargas.
- Jobs de cola: procesamiento SAT, validacion de estatus CFDI y alerta de cancelacion.
- Servicios de dominio: SAT Web Service, parser CFDI, validador e.firma, storage cifrado, reportes fiscales y PAC.
- Modelos base: `User`, `Rfc`, `Cfdi`, `CfdiConcepto`, `DescargaJob`, `Suscripcion`.
- Assets publicos: `efirma.js`, `descarga.js`, `dashboard.js`.
- Vistas Blade para dashboard, CFDI, descargas, RFC, planes y pantallas auth base.

En Hostinger con PHP CLI 8.0, sube los archivos y limpia cache desde hPanel si esta disponible. No ejecutes `php artisan` por SSH.
