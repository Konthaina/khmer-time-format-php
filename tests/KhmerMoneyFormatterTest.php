<?php

namespace KhmerTimeFormat\Tests;

use KhmerTimeFormat\KhmerMoneyFormatter;
use PHPUnit\Framework\TestCase;

final class KhmerMoneyFormatterTest extends TestCase
{
    public function testFormatKhrDefaultUsesKhmerDigits(): void
    {
        $this->assertSame('១៥,០០០ ៛', KhmerMoneyFormatter::formatKHR(15000));
    }

    public function testFormatKhrCanUseLatinDigits(): void
    {
        $this->assertSame('15,000 ៛', KhmerMoneyFormatter::formatKHR(15000, false));
    }

    public function testFormatUsdWithTwoDecimals(): void
    {
        $this->assertSame('$15,000.00', KhmerMoneyFormatter::formatUSD(15000));
    }

    public function testFormatUsdCanUseKhmerDigits(): void
    {
        $this->assertSame('$១៥,០០០.២៥', KhmerMoneyFormatter::formatUSD(15000.25, true));
    }

    public function testToKhmerWordsKhr(): void
    {
        $this->assertSame('មួយម៉ឺនប្រាំពាន់ រៀល', KhmerMoneyFormatter::toKhmerWordsKHR(15000));
    }

    public function testToKhmerWordsUsdWholeAmount(): void
    {
        $this->assertSame('មួយម៉ឺនប្រាំពាន់ ដុល្លារ', KhmerMoneyFormatter::toKhmerWordsUSD(15000));
    }

    public function testToKhmerWordsUsdWithCents(): void
    {
        $this->assertSame('មួយរយម្ភៃបី ដុល្លារ និង សែសិបប្រាំ សេន', KhmerMoneyFormatter::toKhmerWordsUSD(123.45));
    }

    public function testFormatRoundsKhrToNoDecimals(): void
    {
        $this->assertSame('១៥,០០១ ៛', KhmerMoneyFormatter::formatKHR(15000.5));
    }

    public function testGenericFormatUsesCurrencyDefaults(): void
    {
        $this->assertSame('១៥,០០០ ៛', KhmerMoneyFormatter::format('KHR', 15000));
        $this->assertSame('$15,000.00', KhmerMoneyFormatter::format('USD', 15000));
    }

    public function testFormatRejectsUnsupportedCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        KhmerMoneyFormatter::format('EUR', 10);
    }

    public function testFormatRejectsInvalidAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        KhmerMoneyFormatter::formatKHR('abc');
    }
}
