<?php
// Load .env file into constants.
// Called once from bootstrap.php — do not call directly elsewhere.

$env_file = dirname(__DIR__, 2) . '/.env';

if (!file_exists($env_file)) {
    die('Missing .env file. Copy .env.example to .env and fill in values.');
}

$lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    // skip comments
    if (str_starts_with(trim($line), '#')) continue;

    if (!str_contains($line, '=')) continue;

    [$key, $value] = explode('=', $line, 2);
    $key   = trim($key);
    $value = trim($value, " \t\n\r\0\x0B\"");

    if ($key === '') continue;

    if (!defined($key)) {
        define($key, $value);
    }
}
