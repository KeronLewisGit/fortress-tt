<?php
/**
 * One-time installer for hosts without SSH. Visit /setup.php?token=YOUR_SETUP_TOKEN
 * after uploading the package and filling in fortress-app/.env. Deletes itself when done.
 */
$appPath = __DIR__.'/fortress-app';
$envFile = $appPath.'/.env';
header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><meta name="viewport" content="width=device-width"><title>Fortress setup</title><body style="font-family:system-ui;max-width:640px;margin:40px auto;padding:0 16px;line-height:1.5">';
echo '<h1>Fortress Security Services – installer</h1>';

if (! file_exists($envFile)) {
    exit('<p style="color:#b00">fortress-app/.env not found. Copy .env.example to .env and fill in the database details first.</p>');
}
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim(trim($v), "\"'");
}
$token = $env['SETUP_TOKEN'] ?? '';
if ($token === '' || ! isset($_GET['token']) || ! hash_equals($token, $_GET['token'])) {
    exit('<p style="color:#b00">Invalid or missing token. Set SETUP_TOKEN in .env and open /setup.php?token=THAT_VALUE.</p>');
}

require $appPath.'/vendor/autoload.php';
$app = require $appPath.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$run = function (string $cmd, array $args = []) use ($kernel) {
    $out = new Symfony\Component\Console\Output\BufferedOutput;
    $code = $kernel->call($cmd, $args, $out);
    echo '<pre style="background:#f4f4f4;padding:8px;font-size:12px">$ php artisan '.htmlspecialchars($cmd).'  → '.($code === 0 ? 'OK' : 'FAILED')."\n".htmlspecialchars($out->fetch()).'</pre>';

    return $code === 0;
};

$ok = true;
if (empty($env['APP_KEY'])) {
    $ok = $run('key:generate', ['--force' => true]) && $ok;
}
$ok = $run('migrate', ['--force' => true]) && $ok;
$ok = $run('db:seed', ['--force' => true]) && $ok;
$run('optimize');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ok) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 8) {
        $run('app:create-admin', ['--name' => $name ?: 'Administrator', '--email' => $email, '--password' => $pass]);
        $removed = @unlink(__FILE__);
        echo '<p style="color:#070"><strong>Done.</strong> Administrator created. <a href="/login">Sign in</a>, then delete the SETUP_TOKEN line from .env.</p>';
        if (! $removed) {
            echo '<p style="color:#b00;font-weight:bold">setup.php could not delete itself. Delete public_html/setup.php now in File Manager.</p>';
        }
        exit;
    }
    echo '<p style="color:#b00">Please enter a valid email and a password of at least 8 characters.</p>';
}
echo '<h2>Create the first administrator</h2><form method="post"><p><input name="name" placeholder="Name" required style="width:100%;padding:8px"></p><p><input name="email" type="email" placeholder="Email" required style="width:100%;padding:8px"></p><p><input name="password" type="password" placeholder="Password (min 8)" required style="width:100%;padding:8px"></p><button style="padding:10px 18px">Create administrator &amp; finish</button></form>';
