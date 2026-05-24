<?php

namespace PHPlexus\Tests\Core\Logging;

use PHPlexus\Logging\StreamLogger;
use PHPUnit\Framework\TestCase;

class StreamLoggerTest extends TestCase
{
    private string $tempStdout;
    private string $tempStderr;

    protected function setUp(): void
    {
        $this->tempStdout = tempnam(sys_get_temp_dir(), 'stdout');
        $this->tempStderr = tempnam(sys_get_temp_dir(), 'stderr');
    }

    protected function tearDown(): void
    {
        @unlink($this->tempStdout);
        @unlink($this->tempStderr);
    }

    public function testStreamLoggerSplitsStreams()
    {
        $logger = new StreamLogger($this->tempStdout, $this->tempStderr);

        $logger->info('this is info');
        $logger->error('this is error');

        // Force destruction to close resources and flush buffer
        unset($logger);

        $stdoutContent = file_get_contents($this->tempStdout);
        $stderrContent = file_get_contents($this->tempStderr);

        $this->assertStringContainsString('[INFO] this is info', $stdoutContent);
        $this->assertStringNotContainsString('this is error', $stdoutContent);

        $this->assertStringContainsString('[ERROR] this is error', $stderrContent);
        $this->assertStringNotContainsString('this is info', $stderrContent);
    }

    public function testSanitizesNewlines()
    {
        $logger = new StreamLogger($this->tempStdout, $this->tempStderr);
        $logger->info("multi\nline\rmessage");

        unset($logger);

        $stdoutContent = file_get_contents($this->tempStdout);
        $this->assertStringContainsString('multi line message', $stdoutContent);
        $this->assertStringNotContainsString("\nline", $stdoutContent);
    }
}
