<?php

namespace PHPlexus\Logging;

use PHPlexus\Interfaces\LoggerInterface;

class FileLogger implements LoggerInterface
{
    protected $filePath;
    protected $colors = [
        'info' => "\e[34m",
        // Blue
        'debug' => "\e[36m",
        // Cyan
        'error' => "\e[31m",
        // Red
        'warning' => "\e[33m" // Yellow
    ];

    public function __construct(string $filePath)
    {
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


    public function log(string $level, string $message, array $context = []): void
    {
        $formattedMessage = $this->formatMessage($level, $message, $context);
        file_put_contents($this->filePath, $formattedMessage, FILE_APPEND);
    }

    protected function formatMessage(string $level, string $message, array $context): string
    {
        $color = $this->colors[$level] ?? '';
        $resetColor = "\e[0m";
        $date = date('Y-m-d H:i:s');

        // Wrap message with color
        $message = $color . "[#{$level} {$date}#] {$message} " . json_encode($context) . $resetColor . PHP_EOL;

        return $message;
    }

    // Reflective methods
    public function __call($method, $args)
    {
        if (strpos($method, 'log') === 0) {
            $level = strtolower(substr($method, 3));
            $this->log($level, $args[0], $args[1] ?? []);
        }
    }
}