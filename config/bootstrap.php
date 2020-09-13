<?php
declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (! class_exists(Dotenv::class)) {
    throw new LogicException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
}

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
$env = @include dirname(__DIR__).'/.env.local.php';

if (is_array($env) && (!isset($env['APP_ENV']) || ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV']) === $env['APP_ENV'])) {
    (new Dotenv())->populate($env);
} else {
    // load all the .env files
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
}

$_SERVER += $_ENV;

if ($_SERVER['APP_ENV'] !== null) {
    $_ENV['APP_ENV'] = $_SERVER['APP_ENV'];
} elseif ($_ENV['APP_ENV'] !== null) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'];
}

$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_ENV'] !== 'prod';

if (is_int($_SERVER['APP_DEBUG'])) {
    $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'];
} else {
    $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
}
