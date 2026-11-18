<?php

// Prepare writable /tmp directories for Laravel in serverless environment (Vercel)
$storageBase = getenv('APP_STORAGE_PATH') ?: '/tmp/storage';

$directories = [
    $storageBase,
    $storageBase . '/app',
    $storageBase . '/app/public',
    $storageBase . '/framework',
    $storageBase . '/framework/cache',
    $storageBase . '/framework/cache/data',
    $storageBase . '/framework/sessions',
    $storageBase . '/framework/views',
    $storageBase . '/logs',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward Vercel serverless requests to Laravel's public entry point
require __DIR__ . '/../public/index.php';
