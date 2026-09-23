<?php

namespace Tests\Unit;

use App\Services\SalaryCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SalaryCalculatorTest extends TestCase
{
    /** A "now" well after all test days, so no day counts as still in progress. */
    private function later(): Carbon
    {
        return Carbon::parse('2026-10-01 00:00:00');
    }

    private function day(string $in, ?string $out, string $shiftEnd = '18:30:00', ?string $pStart = null, ?string $pEnd = null, ?Carbon $now = null): array
    {
        return SalaryCalculator::day(
            Carbon::parse($in),
            $out ? Carbon::parse($out) : null,
            $shiftEnd,
            $pStart ? Carbon::parse($pStart) : null,
            $pEnd ? Carbon::parse($pEnd) : null,
            $now ?? $this->later()
        );
    }

    public function test_shift_minutes(): void
    {
        $this->assertSame(705, SalaryCalculator::shiftMinutes('09:45:00', '21:30:00'));
        $this->assertSame(540, SalaryCalculator::shiftMinutes('21:00:00', '06:00:00')); // runs past midnight
    }

    public function test_punch_out_pays_punch_in_to_punch_out(): void
    {
        $d = $this->day('2026-09-10 09:30:00', '2026-09-10 18:45:00');
        $this->assertSame('punch_out', $d['endReason']);
        $this->assertSame(555, $d['paid']); // 9h15m, including time before and after the shift
    }

    public function test_missing_punch_out_stops_at_shift_end(): void
    {
        $d = $this->day('2026-09-10 09:50:00', null, '18:30:00');
        $this->assertSame('shift_end', $d['endReason']);
        $this->assertSame(520, $d['paid']); // 09:50 -> 18:30
    }

    public function test_punch_out_after_midnight_is_capped_at_midnight(): void
    {
        $d = $this->day('2026-09-10 20:00:00', '2026-09-11 01:30:00');
        $this->assertSame('midnight', $d['endReason']);
        $this->assertSame(240, $d['paid']); // 20:00 -> 24:00
    }

    public function test_missing_punch_out_after_shift_end_pays_nothing(): void
    {
        $d = $this->day('2026-09-10 19:00:00', null, '18:30:00');
        $this->assertSame('after_shift', $d['endReason']);
        $this->assertSame(0, $d['paid']);
    }

    public function test_permission_minutes_are_deducted(): void
    {
        $d = $this->day('2026-09-10 09:00:00', '2026-09-10 18:00:00', '18:30:00', '2026-09-10 12:00:00', '2026-09-10 13:30:00');
        $this->assertSame(540, $d['worked']);
        $this->assertSame(90, $d['permission']);
        $this->assertSame(450, $d['paid']);
    }

    public function test_unclosed_permission_runs_to_end_of_day(): void
    {
        $d = $this->day('2026-09-10 09:00:00', null, '18:00:00', '2026-09-10 17:00:00', null);
        $this->assertSame(60, $d['permission']);
        $this->assertSame(480, $d['paid']);
    }

    public function test_today_without_punch_out_counts_only_up_to_now(): void
    {
        $d = $this->day('2026-09-23 09:00:00', null, '18:00:00', null, null, Carbon::parse('2026-09-23 11:00:00'));
        $this->assertSame('in_progress', $d['endReason']);
        $this->assertSame(120, $d['paid']);
    }

    public function test_hours_minutes_format(): void
    {
        $this->assertSame('2:05', SalaryCalculator::hm(125));
        $this->assertSame('0:00', SalaryCalculator::hm(0));
        $this->assertSame('-', SalaryCalculator::hm(null));
    }
}
