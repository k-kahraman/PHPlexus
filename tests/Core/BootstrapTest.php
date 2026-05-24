<?php

namespace PHPlexus\Tests\Core;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testLoadEnvVariablesParsesCorrectly()
    {
        $envFile = __DIR__ . '/../../.env';
        $originalEnvContent = file_exists($envFile) ? file_get_contents($envFile) : null;

        $testContent = <<<ENV
# This is a comment
DB_HOST = localhost
DB_PORT=3306 # inline comment
DB_USER="lexus_user"
DB_PASS='lexus_pass'
APP_ENV=override_test
ENV;
        file_put_contents($envFile, $testContent);

        // Pre-set APP_ENV to test no-override behavior
        putenv('APP_ENV=local_dev');
        $_ENV['APP_ENV'] = 'local_dev';
        $_SERVER['APP_ENV'] = 'local_dev';

        require_once __DIR__ . '/../../src/bootstrap.php';

        // Invoke loadEnvVariables defined in bootstrap.php
        \loadEnvVariables();

        $this->assertEquals('localhost', getenv('DB_HOST'));
        $this->assertEquals('3306', getenv('DB_PORT'));
        $this->assertEquals('lexus_user', getenv('DB_USER'));
        $this->assertEquals('lexus_pass', getenv('DB_PASS'));
        $this->assertEquals('local_dev', getenv('APP_ENV')); // should NOT have been overridden

        // Cleanup
        if ($originalEnvContent !== null) {
            file_put_contents($envFile, $originalEnvContent);
        } else {
            @unlink($envFile);
        }
    }
}
