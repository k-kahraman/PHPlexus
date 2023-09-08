<?php

namespace PHPlexus\Interfaces;

interface LoggerInterface
{
    public function log(string $level, string $message, array $context = []): void;
}
