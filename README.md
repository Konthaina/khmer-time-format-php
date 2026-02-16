# khmer-format (PHP)

Khmer formatting utilities.

Current features:
- Time formatter: format `1:22 PM` / `13:22` time into Khmer.
- Money formatter (`khmer-money`): format KHR and USD for Cambodia.

- digits: `ម៉ោង១ និង ២២ នាទី រសៀល`
- words: `ម៉ោងមួយ និង ម្ភៃពីរ នាទី រសៀល`

## Install

```bash
composer require konthaina/khmer-format
```

## Usage

```php

require_once __DIR__ . '/vendor/autoload.php';

use KhmerTimeFormat\KhmerTimeFormatter;

echo KhmerTimeFormatter::format("1:22 PM", "digits");
echo KhmerTimeFormatter::format("13:22", "words");
echo KhmerTimeFormatter::formatNow("digits");
echo KhmerTimeFormatter::formatNow("words", "Asia/Phnom_Penh");
```

`formatNow()` uses the current real system time.  
Pass a timezone like `Asia/Phnom_Penh` when you need a specific timezone.

## Money Usage (`khmer-money`)

```php
use KhmerTimeFormat\KhmerMoneyFormatter;

echo KhmerMoneyFormatter::formatKHR(15000);              // ១៥,០០០ ៛
echo KhmerMoneyFormatter::formatUSD(15000);              // $15,000.00
echo KhmerMoneyFormatter::toKhmerWordsKHR(15000);        // មួយម៉ឺនប្រាំពាន់ រៀល
echo KhmerMoneyFormatter::toKhmerWordsUSD(15000);        // មួយម៉ឺនប្រាំពាន់ ដុល្លារ
echo KhmerMoneyFormatter::format('KHR', 15000, false);   // 15,000 ៛
```

Features:
- Format KHR properly (no decimals).
- Format USD with 2 decimals.
- Convert KHR and USD numbers to Khmer words currency.
- Show symbols correctly (`៛`, `$`).
- Thousands separator and grouped digits.

## Test

```bash
composer install
composer test
```

## Release (GitHub tags + Packagist)

```bash
git tag v0.1.0
git push --tags
```

Then submit your GitHub repo to Packagist (or enable webhook).
