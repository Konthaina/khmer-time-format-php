<?php

namespace KhmerTimeFormat;

final class KhmerTimeFormatter
{
    private const KHMER_DIGITS = [
        '0' => '០','1' => '១','2' => '២','3' => '៣','4' => '៤',
        '5' => '៥','6' => '៦','7' => '៧','8' => '៨','9' => '៩',
    ];

    private const UNITS = [
        0 => 'សូន្យ',
        1 => 'មួយ',
        2 => 'ពីរ',
        3 => 'បី',
        4 => 'បួន',
        5 => 'ប្រាំ',
        6 => 'ប្រាំមួយ',
        7 => 'ប្រាំពីរ',
        8 => 'ប្រាំបី',
        9 => 'ប្រាំបួន',
    ];

    private const TENS = [
        10 => 'ដប់',
        20 => 'ម្ភៃ',
        30 => 'សាមសិប',
        40 => 'សែសិប',
        50 => 'ហាសិប',
    ];

    public static function format(string $time, string $mode = 'digits'): string
    {
        [$hour24, $minute] = self::parseTime($time);
        $hour12 = self::to12h($hour24);
        $period = self::periodKm($hour24);

        if ($mode === 'digits') {
            $h = self::numberToKhmerDigits($hour12);
            $m = self::numberToKhmerDigits($minute);
            return "ម៉ោង{$h} និង {$m} នាទី {$period}";
        }

        if ($mode === 'words') {
            $h = self::numberToKhmerWords($hour12);
            $m = self::numberToKhmerWords($minute);
            return "ម៉ោង{$h} និង {$m} នាទី {$period}";
        }

        throw new \InvalidArgumentException("mode must be 'digits' or 'words'");
    }

    public static function formatNow(string $mode = 'digits', ?string $timezone = null): string
    {
        $dateTimeZone = null;
        if ($timezone !== null) {
            try {
                $dateTimeZone = new \DateTimeZone($timezone);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Invalid timezone identifier.");
            }
        }

        $now = new \DateTimeImmutable('now', $dateTimeZone);
        return self::formatDateTime($now, $mode);
    }

    public static function formatDateTime(\DateTimeInterface $dateTime, string $mode = 'digits'): string
    {
        return self::format($dateTime->format('H:i'), $mode);
    }

    private static function parseTime(string $time): array
    {
        $time = trim($time);
        if (!preg_match('/^(\d{1,2})\s*:\s*(\d{2})\s*(AM|PM)?$/i', $time, $m)) {
            throw new \InvalidArgumentException("Invalid time format. Use 'H:MM', 'HH:MM', or 'H:MM AM/PM'.");
        }

        $hour = intval($m[1]);
        $minute = intval($m[2]);
        $ampm = $m[3] ?? null;

        if ($minute < 0 || $minute > 59) {
            throw new \InvalidArgumentException("Minute must be 00-59.");
        }

        if ($ampm) {
            if ($hour < 1 || $hour > 12) {
                throw new \InvalidArgumentException("Hour must be 1-12 when using AM/PM.");
            }
            $ap = strtoupper($ampm);
            if ($ap === 'AM') {
                $hour24 = ($hour === 12) ? 0 : $hour;
            } else {
                $hour24 = ($hour === 12) ? 12 : $hour + 12;
            }
        } else {
            if ($hour < 0 || $hour > 23) {
                throw new \InvalidArgumentException("Hour must be 0-23 for 24-hour input.");
            }
            $hour24 = $hour;
        }

        return [$hour24, $minute];
    }

    private static function to12h(int $hour24): int
    {
        $h = $hour24 % 12;
        return $h === 0 ? 12 : $h;
    }

    private static function periodKm(int $hour24): string
    {
        if ($hour24 >= 0 && $hour24 <= 5) return 'យប់';
        if ($hour24 >= 6 && $hour24 <= 11) return 'ព្រឹក';
        if ($hour24 >= 12 && $hour24 <= 17) return 'រសៀល';
        return 'ល្ងាច';
    }

    private static function numberToKhmerDigits(int $n): string
    {
        $s = strval($n);
        $out = '';
        for ($i=0; $i<strlen($s); $i++) {
            $ch = $s[$i];
            $out .= self::KHMER_DIGITS[$ch] ?? $ch;
        }
        return $out;
    }

    private static function numberToKhmerWords(int $n): string
    {
        if ($n < 0 || $n > 59) {
            throw new \InvalidArgumentException("number out of supported range (0-59)");
        }
        if ($n < 10) return self::UNITS[$n];
        if ($n < 20) {
            if ($n === 10) return self::TENS[10];
            return self::TENS[10] . self::UNITS[$n - 10];
        }
        $tens = intdiv($n, 10) * 10;
        $ones = $n % 10;
        if ($ones === 0) return self::TENS[$tens];
        return self::TENS[$tens] . self::UNITS[$ones];
    }
}
