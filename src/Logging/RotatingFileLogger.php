<?php

namespace PHPlexus\Logging;

class RotatingFileLogger extends FileLogger
{
    protected $maxFiles;

    public function __construct(string $filePath, int $maxFiles = 10)
    {
        parent::__construct($filePath);
        $this->maxFiles = $maxFiles;
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->rotateIfNeeded();
        parent::log($level, $message, $context);
    }

    protected function rotateIfNeeded(): void
    {
        if (count(glob($this->filePath . '/*.log')) > $this->maxFiles) {
            $this->rotate();
        }
    }

    protected function rotate(): void
    {
        $pathInfo = pathinfo($this->filePath);
        $base = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $ext = $pathInfo['extension'];

        // Rotate existing logs
        for ($i = $this->maxFiles; $i > 0; $i--) {
            $oldFile = "{$base}_{$i}.{$ext}";
            if (file_exists($oldFile)) {
                if ($i === $this->maxFiles) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, "{$base}_" . ($i + 1) . ".{$ext}");
                }
            }
        }

        // Rotate current log
        if (file_exists($this->filePath)) {
            rename($this->filePath, "{$base}_1.{$ext}");
        }
    }
}
