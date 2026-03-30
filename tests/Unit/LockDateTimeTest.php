<?php

namespace Tests\Unit;

use App\Support\LockDateTime;
use Tests\TestCase;

class LockDateTimeTest extends TestCase
{
    public function test_it_converts_winter_local_time_to_utc_milliseconds(): void
    {
        config(['app.timezone' => 'Europe/Lisbon']);

        $expected = strtotime('2026-01-15 10:00:00 UTC') * 1000;

        $this->assertSame($expected, LockDateTime::toUtcMilliseconds('2026-01-15 10:00:00'));
    }

    public function test_it_converts_summer_local_time_to_utc_milliseconds(): void
    {
        config(['app.timezone' => 'Europe/Lisbon']);

        $expected = strtotime('2026-07-15 09:00:00 UTC') * 1000;

        $this->assertSame($expected, LockDateTime::toUtcMilliseconds('2026-07-15 10:00:00'));
    }

    public function test_it_adds_minutes_across_dst_start_without_drifting(): void
    {
        config(['app.timezone' => 'Europe/Lisbon']);

        $this->assertSame('2026-03-29 02:00:00', LockDateTime::addMinutes('2026-03-29 00:30:00', 30));
        $this->assertSame('2026-03-29 03:00:00', LockDateTime::addMinutes('2026-03-29 01:30:00', 30));
    }
}
