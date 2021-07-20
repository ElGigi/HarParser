# HAR Parser

[![Latest Version](https://img.shields.io/packagist/v/elgigi/har-parser.svg?style=flat-square)](https://github.com/elgigi/har-parser/releases)
[![Software license](https://img.shields.io/github/license/elgigi/har-parser.svg?style=flat-square)](https://github.com/elgigi/har-parser/blob/main/LICENSE)
[![Build Status](https://img.shields.io/travis/com/elgigi/har-parser/main.svg?style=flat-square)](https://travis-ci.com/elgigi/har-parser)
[![Quality Grade](https://img.shields.io/codacy/grade/0447a4290de744dc81a7e2cf9891a47d/main.svg?style=flat-square)](https://app.codacy.com/gh/elgigi/har-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/elgigi/har-parser.svg?style=flat-square)](https://packagist.org/packages/elgigi/har-parser)

## Usage

Load you HAR file with an `Parser` object or with `Log` entity.

With `Parser` class:

```php
use ElGigi\HarParser\Parser;

$harFile = new Parser();

$log = $harFile->parse('/path/of/my/file.har', contentIsFile: true);
$log = $harFile->parse(['my' => 'har decoded']);
```

With `Log` entity class:

```php
use ElGigi\HarParser\Entities\Log;

$log = Log::load(json_decode(file_get_contents('/path/of/my/file.har'), true));
```

## Entities

The HAR file is distributed in several entities:

- Log
    - Creator
    - Browser
    - Page[]
        - PageTimings
    - Entry[]
        - Request
            - Cookie[]
            - Header[]
            - PostData
        - Response
            - Cookie[]
            - Header[]
            - Content
        - Timings