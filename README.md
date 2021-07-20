# HAR Parser

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