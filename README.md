# Stream Filter for `mb_convert_encoding`

# Requirements

- PHP 7.0+

# Installation

```
composer require mosaxiv/stream-filter-mbencoding
```

# Usage

```php
use StreamFilter\ConvertMbEncoding;
use SplFileObject;

ConvertMbEncoding::register();
$url = ConvertMbEncoding::getFilterURL('test.csv', 'UTF-8', 'SJIS-WIN');
$file = new SplFileObject($url);
```
