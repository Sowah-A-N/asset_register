<?php
/**
 * config/env.php — Lightweight .env file loader.
 *
 * Reads KEY=VALUE lines from /.env (two directories above this file,
 * i.e. the project root) and injects them into $_ENV / putenv().
 * Lines that start with # are treated as comments.
 * Values already set in the environment (real server env-vars) are never
 * overridden, so production deployments can use real env-vars without a
 * .env file on disk.
 *
 * Call require_once __DIR__ . '/env.php' once, early — e.g. from
 * config/database.php which is itself included by every datacon.php.
 */

(static function (): void {
    $envFile = dirname(__DIR__, 2) . '/.env';   // project root/.env
    if (!file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\"'");   // strip optional surrounding quotes

        if ($key === '' || array_key_exists($key, $_ENV)) {
            continue;   // never overwrite values already in the environment
        }

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
})();
