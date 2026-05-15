<?php

declare(strict_types=1);

$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') : '';
$rootCandidates = array_unique(array_filter([
    dirname(__DIR__),
    __DIR__,
    $documentRoot,
    $documentRoot !== '' ? dirname($documentRoot) : '',
    $documentRoot !== '' ? dirname($documentRoot).'/public_html' : '',
]));

$root = null;

foreach ($rootCandidates as $candidate) {
    if (
        file_exists($candidate.'/composer.json')
        && file_exists($candidate.'/bootstrap/app.php')
        && file_exists($candidate.'/vendor/autoload.php')
    ) {
        $root = $candidate;
        break;
    }
}

$fallbackRoot = file_exists(dirname(__DIR__).'/composer.json') ? dirname(__DIR__) : __DIR__;
$root ??= $fallbackRoot;

$envPath = $root.'/.env';
$envExamplePath = $root.'/.env.example';
$vendorPath = $root.'/vendor/autoload.php';
$installerPassword = 'cambia-esta-clave';
$errors = [];
$messages = [];

function env_value(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return '';
    }

    if (preg_match('/\s|#|"|\'/', $value) === 1) {
        return '"'.str_replace('"', '\"', $value).'"';
    }

    return $value;
}

function update_env(string $contents, array $values): string
{
    foreach ($values as $key => $value) {
        $line = $key.'='.env_value((string) $value);

        if (preg_match('/^'.$key.'=.*$/m', $contents) === 1) {
            $contents = preg_replace('/^'.$key.'=.*$/m', $line, $contents);
            continue;
        }

        $contents .= PHP_EOL.$line;
    }

    return $contents;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (! hash_equals($installerPassword, (string) ($_POST['installer_password'] ?? ''))) {
        $errors[] = 'Clave del instalador incorrecta. Edita public/hostinger-install.php y cambia $installerPassword antes de usarlo.';
    }

    if (PHP_VERSION_ID < 80200) {
        $errors[] = 'El PHP web debe ser 8.2 o superior. Version detectada: '.PHP_VERSION;
    }

    if (! file_exists($vendorPath)) {
        $errors[] = 'No existe vendor/autoload.php en la ruta detectada: '.$root.'. Si ya instalaste Composer, el instalador esta en otra carpeta. Busca la ruta con: find $HOME -name autoload.php | grep vendor';
    }

    if (! file_exists($envExamplePath)) {
        $errors[] = 'No existe .env.example en la raiz del proyecto.';
    }

    $dbName = trim((string) ($_POST['db_database'] ?? ''));
    $dbUser = trim((string) ($_POST['db_username'] ?? ''));
    $dbPassword = (string) ($_POST['db_password'] ?? '');
    $dbHost = trim((string) ($_POST['db_host'] ?? '127.0.0.1'));
    $appUrl = trim((string) ($_POST['app_url'] ?? ''));

    if ($dbName === '' || $dbUser === '' || $appUrl === '') {
        $errors[] = 'Completa APP_URL, DB_DATABASE y DB_USERNAME.';
    }

    if ($errors === []) {
        try {
            $pdo = new PDO(
                dsn: "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
                username: $dbUser,
                password: $dbPassword,
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            );
            $pdo->query('select 1');
            $messages[] = 'Conexion MySQL correcta.';
        } catch (Throwable $exception) {
            $errors[] = 'MySQL no conecto: '.$exception->getMessage();
        }
    }

    if ($errors === []) {
        $env = file_exists($envPath)
            ? (string) file_get_contents($envPath)
            : (string) file_get_contents($envExamplePath);

        $env = update_env($env, [
            'APP_NAME' => 'ContadorMx',
            'APP_ENV' => 'production',
            'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'APP_DEBUG' => 'false',
            'APP_URL' => $appUrl,
            'APP_FORCE_HTTPS' => 'true',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $dbHost,
            'DB_PORT' => '3306',
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPassword,
            'SESSION_DRIVER' => 'database',
            'SESSION_ENCRYPT' => 'true',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'database',
            'FILESYSTEM_DISK' => 'private',
        ]);

        if (file_put_contents($envPath, $env) === false) {
            $errors[] = 'No se pudo escribir .env. Revisa permisos de la carpeta raiz.';
        } else {
            $messages[] = '.env creado/actualizado.';
        }
    }

    if ($errors === []) {
        try {
            require $vendorPath;

            $app = require $root.'/bootstrap/app.php';
            $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

            Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $messages[] = nl2br(e(Illuminate\Support\Facades\Artisan::output()));

            Illuminate\Support\Facades\Artisan::call('config:cache');
            Illuminate\Support\Facades\Artisan::call('route:cache');
            Illuminate\Support\Facades\Artisan::call('view:cache');
            $messages[] = 'Cache de Laravel generado.';
        } catch (Throwable $exception) {
            $errors[] = 'Laravel no pudo instalarse desde web: '.$exception->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalador Hostinger - ContadorMx</title>
    <style>
        body { background: #f5f7fb; color: #172033; font-family: system-ui, sans-serif; margin: 0; }
        main { margin: 32px auto; max-width: 760px; padding: 0 16px; }
        section { background: #fff; border: 1px solid #dbe3ef; border-radius: 8px; padding: 24px; }
        label { display: block; font-weight: 700; margin-top: 14px; }
        input { border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font: inherit; padding: 10px; width: 100%; }
        button { background: #1d4ed8; border: 0; border-radius: 6px; color: #fff; cursor: pointer; font: inherit; font-weight: 700; margin-top: 18px; padding: 11px 16px; }
        .alert { border-radius: 6px; margin: 12px 0; padding: 12px; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #ecfdf5; border: 1px solid #bbf7d0; color: #065f46; }
        .muted { color: #5f6f85; }
        code { background: #eef2ff; border-radius: 4px; padding: 2px 5px; }
    </style>
</head>
<body>
<main>
    <section>
        <h1>Instalador Hostinger - ContadorMx</h1>
        <p class="muted">Este archivo temporal ejecuta la instalacion con PHP web <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?>.</p>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>

        <?php foreach ($messages as $message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endforeach; ?>

        <form method="post">
            <p class="muted">Ruta detectada del proyecto: <code><?= htmlspecialchars($root, ENT_QUOTES, 'UTF-8') ?></code></p>
            <p class="muted">Vendor esperado: <code><?= htmlspecialchars($vendorPath, ENT_QUOTES, 'UTF-8') ?></code></p>

            <label for="installer_password">Clave del instalador</label>
            <input id="installer_password" name="installer_password" type="password" required>

            <label for="app_url">APP_URL</label>
            <input id="app_url" name="app_url" type="url" value="https://xml.contadormx.net" required>

            <label for="db_host">DB_HOST</label>
            <input id="db_host" name="db_host" value="127.0.0.1" required>

            <label for="db_database">DB_DATABASE</label>
            <input id="db_database" name="db_database" required>

            <label for="db_username">DB_USERNAME</label>
            <input id="db_username" name="db_username" required>

            <label for="db_password">DB_PASSWORD</label>
            <input id="db_password" name="db_password" type="password">

            <button type="submit">Instalar Laravel</button>
        </form>

        <p class="muted">Cuando termine, elimina <code>public/hostinger-install.php</code>.</p>
    </section>
</main>
</body>
</html>
