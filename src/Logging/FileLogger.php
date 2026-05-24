<?php

namespace PHPlexus\Logging;

use Psr\Log\AbstractLogger;
use PHPlexus\Interfaces\LoggerInterface;

class FileLogger extends AbstractLogger implements LoggerInterface {
    protected string $filePath;
    protected array $colors = [
        'info' => "\e[34m",
        // Blue
        'debug' => "\e[36m",
        // Cyan
        'error' => "\e[31m",
        // Red
        'warning' => "\e[33m" // Yellow
    ];

    public function __construct(string $filePath) {
        if (empty($filePath)) {
            throw new \InvalidArgumentException("File path cannot be empty.");
        }

        $this->filePath = $filePath;

        // Ensure directory exists
        $dirPath = dirname($this->filePath);
        if (!file_exists($dirPath)) {
            if (false === mkdir($dirPath, 0777, true)) { // recursive directory creation
                throw new \RuntimeException("Failed to create directory: {$dirPath}");
            }
        }

        if (!is_writable($dirPath)) {
            throw new \RuntimeException("Directory {$dirPath} is not writable.");
        }

        // If file does not exist, attempt to create it
        if (!file_exists($this->filePath)) {
            if (false === touch($this->filePath)) {
                throw new \RuntimeException("Failed to create file: {$this->filePath}");
            }
        }

        // Ensure that the log file is writable
        if (!is_writable($this->filePath)) {
            throw new \RuntimeException("File {$this->filePath} is not writable.");
        }
    }

    public function log(mixed $level, mixed $message, array $context = []): void {
        $levelStr = is_string($level) ? $level : (is_scalar($level) ? strval($level) : json_encode($level));
        $messageStr = is_string($message) ? $message : ($message instanceof \Stringable ? $message->__toString() : json_encode($message));
        $formattedMessage = $this->formatMessage($levelStr, $messageStr, $context);
        file_put_contents($this->filePath, $formattedMessage, FILE_APPEND);
    }

    protected function formatMessage(string $level, string $message, array $context): string {
        $color = $this->colors[$level] ?? '';
        $resetColor = "\e[0m";
        $date = date('Y-m-d H:i:s');

        // Wrap message with color
        $message = $color . "[#{$level} {$date}#] {$message} " . json_encode($context) . $resetColor . PHP_EOL;

        return $message;
    }
}