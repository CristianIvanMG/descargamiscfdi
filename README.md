# ContadorMx - Paso 1

Base Laravel 11 para validar que Hostinger Business acepta el stack definido para `xml.contadormx.net`.

## Requisitos del servidor

- PHP 8.2 o superior
- Composer 2
- MySQL 8
- Extensiones PHP comunes de Laravel: `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`
- SSH habilitado
- Cron Jobs habilitados

## Instalacion por SSH en Hostinger

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

Ya dentro de la carpeta que contiene `composer.json`, ejecutar:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

En `.env`, completar los valores reales de MySQL de Hostinger:

```dotenv
DB_DATABASE=nombre_base
DB_USERNAME=usuario_base
DB_PASSWORD=password_base
```

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
