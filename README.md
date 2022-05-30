# HAR Parser

[![Latest Version](https://img.shields.io/packagist/v/elgigi/har-parser.svg?style=flat-square)](https://github.com/ElGigi/HarParser/releases)
[![Software license](https://img.shields.io/github/license/ElGigi/HarParser.svg?style=flat-square)](https://github.com/ElGigi/HarParser/blob/main/LICENSE)
[![Build Status](https://img.shields.io/github/workflow/status/ElGigi/HarParser/Tests/main.svg?style=flat-square)](https://github.com/ElGigi/HarParser/actions/workflows/tests.yml?query=branch%3Amain)
[![Quality Grade](https://img.shields.io/codacy/grade/0447a4290de744dc81a7e2cf9891a47d/main.svg?style=flat-square)](https://app.codacy.com/gh/ElGigi/HarParser)
[![Total Downloads](https://img.shields.io/packagist/dt/elgigi/har-parser.svg?style=flat-square)](https://packagist.org/packages/elgigi/har-parser)

Library to parse and generate [HAR file format](https://en.wikipedia.org/wiki/HAR_(file_format)).

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

## Builder

Two builders are available to construct an HAR file from entities:

- `Builder`: build a `Log` entity from others entities
- `BuilderStream`: build directly the JSON file in stream to prevent memory usage

Both implements `BuilderInterface`:

- `BuilderInterface::reset(): void`: reset builder data
- `BuilderInterface::setVersion(string $version): void`: define version of HAR file (default: 1.2)
- `BuilderInterface::setCreator(string $creator): void`: set creator entity
- `BuilderInterface::setBrowser(string $browser): void`: set browser entity
- `BuilderInterface::addPage(Page ...$page): void`: add a page entity (or multiple pages)
- `BuilderInterface::addEntry(Entry ...$entry): void`: add an entry entity (or multiple entries)
- `BuilderInterface::setComment(?string $comment): void`: define comment of HAR file

For stream builder, the constructor attempt a valid resource (writeable and seekable).

For standard builder, the constructor accept an HAR file, for example, complete an existent HAR.

## Anonymize HAR

In some cases, like unit tests, you need to anonymize your HAR file.

The `Anonymizer` class it's do for that!

```php
class Anonymizer
{
    /**
     * Add header to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addHeaderToRedact(string ...$regex): void;

    /**
     * Add query string to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addQueryStringToRedact(string ...$regex): void;

    /**
     * Add post data to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addPostDataToRedact(string ...$regex): void;

    /**
     * Add accepted mime.
     *
     * @param string ...$mime
     *
     * @return void
     */
    public function addAcceptedMime(string ...$mime): void;

    /**
     * Add content to redact.
     *
     * @param array $contents
     *
     * @return void
     */
    public function addContentToRedact(array $contents): void;

    /**
     * Add callback.
     *
     * @param callable ...$callback
     *
     * @return void
     */
    public function addCallback(callable ...$callback): void;

    /**
     * Anonymize HAR file.
     *
     * @param Log $log
     *
     * @return Log
     * @throws InvalidArgumentException
     */
    public function anonymize(Log $log): Log;
}
```