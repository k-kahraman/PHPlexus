<?php

// Autoload our classes using Composer's autoload
require_once __DIR__ . '/../vendor/autoload.php';

use RepoScribe\Logging\RotatingFileLogger;

// Load environment variables
loadEnvVariables();

// Set up Logger
$logPath = '/var/www/html/logs/php.log';
$maxFiles = 10;
$logger = new RotatingFileLogger($logPath, $maxFiles);

// Handle Exceptions & Errors with Logger
set_exception_handler(function ($exception) use ($logger) {
    $logger->logWarning($exception->getMessage());
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
    $errorMsg = "$errstr in $errfile on line $errline";
    $logger->logError($errorMsg);
});

function loadEnvVariables() {
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
