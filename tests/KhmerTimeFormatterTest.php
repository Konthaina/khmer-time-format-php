<?php

namespace KhmerTimeFormat\Tests;

use KhmerTimeFormat\KhmerTimeFormatter;
use PHPUnit\Framework\TestCase;

final class KhmerTimeFormatterTest extends TestCase
{
    public function testDigits12hPm(): void
    {
        $this->assertSame("ម៉ោង១ និង ២២ នាទី រសៀល", KhmerTimeFormatter::format("1:22 PM", "digits"));
    }

    public function testWords12hPm(): void
    {
        $this->assertSame("ម៉ោងមួយ និង ម្ភៃពីរ នាទី រសៀល", KhmerTimeFormatter::format("1:22 PM", "words"));
    }

    public function testDigits24h(): void
    {
        $this->assertSame("ម៉ោង១ និង ២២ នាទី រសៀល", KhmerTimeFormatter::format("13:22", "digits"));
    }

    public function testWords24h(): void
    {
        $this->assertSame("ម៉ោងមួយ និង ម្ភៃពីរ នាទី រសៀល", KhmerTimeFormatter::format("13:22", "words"));
    }

    public function testFormatDateTimeDigits(): void
    {
        $dateTime = new \DateTimeImmutable('2025-01-01 13:22:00', new \DateTimeZone('Asia/Phnom_Penh'));

        $this->assertSame("ម៉ោង១ និង ២២ នាទី រសៀល", KhmerTimeFormatter::formatDateTime($dateTime, "digits"));
    }

    public function testFormatDateTimeWords(): void
    {
        $dateTime = new \DateTimeImmutable('2025-01-01 13:22:00', new \DateTimeZone('Asia/Phnom_Penh'));

        $this->assertSame("ម៉ោងមួយ និង ម្ភៃពីរ នាទី រសៀល", KhmerTimeFormatter::formatDateTime($dateTime, "words"));
    }

    public function testFormatNowRejectsInvalidTimezone(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        KhmerTimeFormatter::formatNow("digits", "Not/A_Real_Timezone");
    }
}
