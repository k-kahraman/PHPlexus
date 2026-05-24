<?php

function asset(string $path): string {
    return '/assets/' . $path;
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}