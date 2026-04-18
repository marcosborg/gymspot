<?php

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

class EnvironmentFile
{
    protected const SWITCHABLE_KEYS = [
        'APP_NAME',
        'APP_ENV',
        'APP_DEBUG',
        'APP_URL',
        'APP_TIMEZONE',
        'DB_CONNECTION',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
    ];

    public static function activePath(): string
    {
        return base_path('.env');
    }

    public static function read(string $path): string
    {
        $contents = @file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read environment file [{$path}].");
        }

        return $contents;
    }

    public static function write(string $path, string $contents): void
    {
        if (@file_put_contents($path, $contents) === false) {
            throw new RuntimeException("Unable to write environment file [{$path}].");
        }
    }

    public static function parse(string $path): array
    {
        return static::parseContents(static::read($path));
    }

    public static function profileConfig(string $environment): array
    {
        $path = static::activePath();
        $config = static::parse($path);
        $prefix = strtoupper($environment) . '_';
        $profile = [];

        foreach (static::SWITCHABLE_KEYS as $key) {
            $profileKey = $prefix . $key;

            if (array_key_exists($profileKey, $config)) {
                $profile[$key] = $config[$profileKey];
            }
        }

        if ($profile === []) {
            throw new RuntimeException("Profile [{$environment}] was not found in [{$path}].");
        }

        return [
            'environment' => $environment,
            'path' => $path,
            'values' => $profile,
        ];
    }

    public static function switchToProfile(string $environment): void
    {
        $profile = static::profileConfig($environment);
        $currentContents = static::read(static::activePath());
        $updatedContents = static::setValues($currentContents, $profile['values']);

        static::write(static::activePath(), $updatedContents);
    }

    public static function databaseConfig(string $environment): array
    {
        $profile = static::profileConfig($environment);
        $config = $profile['values'];
        $connection = $config['DB_CONNECTION'] ?? 'mysql';

        if ($connection !== 'mysql') {
            throw new InvalidArgumentException("Environment [{$environment}] uses DB_CONNECTION [{$connection}], but only mysql is supported by this command.");
        }

        foreach (['DB_HOST', 'DB_DATABASE', 'DB_USERNAME'] as $requiredKey) {
            if (($config[$requiredKey] ?? '') === '') {
                throw new InvalidArgumentException("Environment [{$environment}] is missing required key [{$requiredKey}] in [{$profile['path']}].");
            }
        }

        return [
            'environment' => $environment,
            'path' => $profile['path'],
            'host' => $config['DB_HOST'],
            'port' => $config['DB_PORT'] ?? '3306',
            'database' => $config['DB_DATABASE'],
            'username' => $config['DB_USERNAME'],
            'password' => $config['DB_PASSWORD'] ?? '',
            'charset' => $config['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $config['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        ];
    }

    protected static function parseContents(string $contents): array
    {
        $values = [];

        foreach (preg_split("/\r\n|\n|\r/", $contents) as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (str_starts_with($trimmed, 'export ')) {
                $trimmed = substr($trimmed, 7);
            }

            $parts = explode('=', $trimmed, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            if ($key === '') {
                continue;
            }

            $values[$key] = static::normalizeValue($value);
        }

        return $values;
    }

    protected static function setValues(string $contents, array $pairs): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $contents);
        $handled = [];

        foreach ($lines as $index => $line) {
            foreach ($pairs as $key => $value) {
                if (preg_match('/^\s*' . preg_quote($key, '/') . '\s*=/', $line) === 1) {
                    $lines[$index] = $key . '=' . static::formatValue($value);
                    $handled[$key] = true;
                    break;
                }
            }
        }

        foreach ($pairs as $key => $value) {
            if (!isset($handled[$key])) {
                $lines[] = $key . '=' . static::formatValue($value);
            }
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected static function normalizeValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $quote = $value[0];

        if (($quote === '"' || $quote === "'") && substr($value, -1) === $quote) {
            $value = substr($value, 1, -1);

            if ($quote === '"') {
                return stripcslashes($value);
            }
        }

        return $value;
    }

    protected static function formatValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/^[A-Za-z0-9_:\\/.@-]+$/', $value) === 1) {
            return $value;
        }

        return '"' . addcslashes($value, "\\\"") . '"';
    }
}
