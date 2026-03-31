<?php

namespace App\Support;

use Carbon\CarbonImmutable;

class LockDateTime
{
    public static function timezone(): string
    {
        return (string) config('app.timezone', 'Europe/Lisbon');
    }

    public static function fromLocalString(string $value): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value, self::timezone());
    }

    public static function addMinutes(string $value, int $minutes): string
    {
        return self::fromLocalString($value)
            ->addMinutes($minutes)
            ->format('Y-m-d H:i:s');
    }

    public static function toLockUnixTimestamp(string $value): int
    {
        return self::fromLocalString($value)
            ->utc()
            ->getTimestamp();
    }
}
