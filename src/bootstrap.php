<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
loadEnvVariables();

// Load configuration and DI container
$config = require __DIR__ . '/../config/config.php';
$container = require __DIR__ . '/../di/services.php';

// Pass configuration to container
$container->instance('config', $config);

// Retrieve the logger singleton from the DI container
$logger = $container->make(PHPlexus\Logging\RotatingFileLogger::class);

// Handle Exceptions & Errors with Logger
set_exception_handler(function ($exception) use ($logger) {
    $logger->logWarning($exception->getMessage());
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
    $errorMsg = "$errstr in $errfile on line $errline";
    $logger->logError($errorMsg);
});

function loadEnvVariables()
{
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                putenv("$name=$value");
            }
        }
    }
}

return $container;