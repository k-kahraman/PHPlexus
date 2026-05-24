<?php

namespace PHPlexus\Logging;

use Psr\Log\AbstractLogger;
use PHPlexus\Interfaces\LoggerInterface;

class StreamLogger extends AbstractLogger implements LoggerInterface {
    private mixed $stdout;
    private mixed $stderr;

    public function __construct(string $stdoutStream = 'php://stdout', string $stderrStream = 'php://stderr') {
        $this->stdout = fopen($stdoutStream, 'wb');
        $this->stderr = fopen($stderrStream, 'wb');
    }

    public function log(mixed $level, mixed $message, array $context = []): void {
        $messageStr = is_string($message) ? $message : ($message instanceof \Stringable ? $message->__toString() : json_encode($message));
        $messageStr = str_replace(["\r", "\n"], ' ', $messageStr);

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $levelStr = is_string($level) ? $level : (is_scalar($level) ? strval($level) : json_encode($level));
        $formatted = sprintf("[%s] [%s] %s%s\n", $timestamp, strtoupper($levelStr), $messageStr, $contextStr);

        $stream = $this->isErrorLevel($level) ? $this->stderr : $this->stdout;

        if ($stream) {
            fwrite($stream, $formatted);
        }
    }

    private function isErrorLevel(mixed $level): bool {
        $levelStr = is_string($level) ? $level : (is_scalar($level) ? strval($level) : json_encode($level));
        $levelStr = strtolower($levelStr);
        return in_array($levelStr, ['error', 'warning', 'critical', 'alert', 'emergency'], true);
    }

    public function __destruct() {
        if (is_resource($this->stdout)) {
            fclose($this->stdout);
        }
        if (is_resource($this->stderr)) {
            fclose($this->stderr);
        }
    }
}
