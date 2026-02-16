<?php

namespace KhmerTimeFormat;

final class KhmerMoneyFormatter
{
    private const KHMER_DIGITS = [
        '0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤',
        '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩',
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
        60 => 'ហុកសិប',
        70 => 'ចិតសិប',
        80 => 'ប៉ែតសិប',
        90 => 'កៅសិប',
    ];

    public static function format(string $currency, int|float|string $amount, ?bool $khmerDigits = null): string
    {
        $currencyCode = strtoupper(trim($currency));

        return match ($currencyCode) {
            'KHR' => self::formatKHR($amount, $khmerDigits ?? true),
            'USD' => self::formatUSD($amount, $khmerDigits ?? false),
            default => throw new \InvalidArgumentException("currency must be 'KHR' or 'USD'"),
        };
    }

    public static function formatKHR(int|float|string $amount, bool $khmerDigits = true): string
    {
        $value = self::normalizeAmount($amount);
        $rounded = (int) round($value, 0, PHP_ROUND_HALF_UP);
        $formatted = number_format(abs($rounded), 0, '.', ',');

        if ($khmerDigits) {
            $formatted = self::toKhmerDigits($formatted);
        }

        $sign = $rounded < 0 ? '-' : '';
        return "{$sign}{$formatted} ៛";
    }

    public static function formatUSD(int|float|string $amount, bool $khmerDigits = false): string
    {
        $value = self::normalizeAmount($amount);
        $rounded = round($value, 2, PHP_ROUND_HALF_UP);
        $formatted = number_format(abs($rounded), 2, '.', ',');

        if ($khmerDigits) {
            $formatted = self::toKhmerDigits($formatted);
        }

        $sign = $rounded < 0 ? '-' : '';
        return "{$sign}\${$formatted}";
    }

    public static function toKhmerWordsKHR(int|float|string $amount): string
    {
        $value = self::normalizeAmount($amount);
        $rounded = (int) round($value, 0, PHP_ROUND_HALF_UP);
        $words = self::integerToKhmerWords(abs($rounded));
        $sign = $rounded < 0 ? 'ដក ' : '';

        return "{$sign}{$words} រៀល";
    }

    public static function toKhmerWordsUSD(int|float|string $amount): string
    {
        $value = self::normalizeAmount($amount);
        $rounded = round($value, 2, PHP_ROUND_HALF_UP);

        $abs = abs($rounded);
        $dollars = (int) floor($abs);
        $cents = (int) round(($abs - $dollars) * 100, 0, PHP_ROUND_HALF_UP);

        if ($cents === 100) {
            $dollars += 1;
            $cents = 0;
        }

        $words = self::integerToKhmerWords($dollars) . ' ដុល្លារ';
        if ($cents > 0) {
            $words .= ' និង ' . self::integerToKhmerWords($cents) . ' សេន';
        }

        if ($rounded < 0) {
            return 'ដក ' . $words;
        }

        return $words;
    }

    private static function normalizeAmount(int|float|string $amount): float
    {
        if (is_string($amount)) {
            $clean = str_replace([',', ' '], '', trim($amount));
            if ($clean === '' || !is_numeric($clean)) {
                throw new \InvalidArgumentException('Amount must be numeric.');
            }
            $value = (float) $clean;
            if (!is_finite($value)) {
                throw new \InvalidArgumentException('Amount must be a finite number.');
            }
            return $value;
        }

        $value = (float) $amount;
        if (!is_finite($value)) {
            throw new \InvalidArgumentException('Amount must be a finite number.');
        }
        return $value;
    }

    private static function toKhmerDigits(string $value): string
    {
        $out = '';
        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $char = $value[$i];
            $out .= self::KHMER_DIGITS[$char] ?? $char;
        }
        return $out;
    }

    private static function integerToKhmerWords(int $number): string
    {
        if ($number === 0) {
            return self::UNITS[0];
        }

        if ($number < 10) {
            return self::UNITS[$number];
        }

        if ($number < 100) {
            $tens = intdiv($number, 10) * 10;
            $ones = $number % 10;
            $words = self::TENS[$tens];
            if ($ones > 0) {
                $words .= self::UNITS[$ones];
            }
            return $words;
        }

        $scales = [
            1000000000 => 'ពាន់លាន',
            1000000 => 'លាន',
            10000 => 'ម៉ឺន',
            1000 => 'ពាន់',
            100 => 'រយ',
        ];

        foreach ($scales as $value => $label) {
            if ($number >= $value) {
                $major = intdiv($number, $value);
                $remainder = $number % $value;

                $words = self::integerToKhmerWords($major) . $label;
                if ($remainder > 0) {
                    $words .= self::integerToKhmerWords($remainder);
                }
                return $words;
            }
        }

        throw new \InvalidArgumentException('Unsupported number.');
    }
}
