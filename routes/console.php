<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('app:sync-version', function () {
    $envPath = base_path('.env');

    if (!file_exists($envPath)) {
        $this->error('.env introuvable');
        return self::FAILURE;
    }

    $env = file_get_contents($envPath);

    // Version = dernier tag (ex: v1.0.8). Si pas de tag => on garde APP_VERSION existant ou 0.0.0
    $version = trim(shell_exec('git describe --tags --abbrev=0 2>/dev/null'));
    if (!$version) {
        $version = env('APP_VERSION', '0.0.0');
    }

    // Build = tag/hash + dirty
    $build = trim(shell_exec('git describe --tags --always --dirty 2>/dev/null'));
    if (!$build) {
        $build = trim(shell_exec('git rev-parse --short HEAD 2>/dev/null'));
    }
    if (!$build) {
        $this->error('git info introuvable');
        return self::FAILURE;
    }

    $env = upsertEnvLine($env, 'APP_VERSION', $version);
    $env = upsertEnvLine($env, 'APP_BUILD', $build);

    file_put_contents($envPath, rtrim($env) . PHP_EOL);

    $this->info("APP_VERSION={$version}");
    $this->info("APP_BUILD={$build}");

    return self::SUCCESS;
})->purpose('Sync APP_VERSION (git tag) + APP_BUILD (git describe) into .env');

function upsertEnvLine(string $content, string $key, string $value): string
{
    $line = $key . '=' . $value;

    if (preg_match('/^' . preg_quote($key, '/') . '=/m', $content)) {
        return preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $content, 1);
    }

    return rtrim($content) . PHP_EOL . $line . PHP_EOL;
}