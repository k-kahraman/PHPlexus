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
$logger = $container->make(PHPlexus\Interfaces\LoggerInterface::class);

// Handle Exceptions & Errors with Logger
set_exception_handler(function (\Throwable $exception) use ($logger): void {
    $logger->error($exception->getMessage(), ['exception' => $exception]);

    if (!headers_sent()) {
        header('Content-Type: application/json', true, 500);
    }

    if (getenv('APP_ENV') === 'production') {
        echo json_encode(['error' => 'Internal Server Error']);
    } else {
        echo json_encode([
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ]);
    }
    exit(1);
});

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) use ($logger): bool {
    $errorMsg = "$errstr in $errfile on line $errline";
    $logger->error($errorMsg, ['errno' => $errno]);

    if (getenv('APP_ENV') === 'production') {
        if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
            if (!headers_sent()) {
                header('Content-Type: application/json', true, 500);
            }
            echo json_encode(['error' => 'Internal Server Error']);
            exit(1);
        }
        return false;
    } else {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
});

function loadEnvVariables(): void {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Strip trailing comments
                if (strpos($value, '#') !== false) {
                    $firstChar = substr($value, 0, 1);
                    if ($firstChar !== '"' && $firstChar !== "'") {
                        list($value, ) = explode('#', $value, 2);
                        $value = trim($value);
                    }
                }

                // Strip quotes
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                // Preserve container environment variables
                if (getenv($name) === false) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

return $container;