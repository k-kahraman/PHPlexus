<?php
use PHPlexus\DI\Container;

$container = new Container();

// Here we can bind our classes, instances, and singletons.
// E.g.
$container->bind(\PHPlexus\Model\Model::class);
$container->bind(\PHPlexus\Repository\Repository::class);
$container->bind(\PHPlexus\Service\Service::class);
$container->bind(\PHPlexus\Controller\Controller::class);
$container->singleton(\PHPlexus\Logging\RotatingFileLogger::class, function($container) {
    $config = $container->make('config');  // Get the configurations from the container
    $logPath = $config['logPath'] ?? '/var/www/html/logs/php.log';  // Default path if not provided in config
    $maxFiles = $config['maxFiles'] ?? 10;  // Default maxFiles if not provided in config
    return new \PHPlexus\Logging\RotatingFileLogger($logPath, $maxFiles);
});

// ... other bindings ...

return $container;
