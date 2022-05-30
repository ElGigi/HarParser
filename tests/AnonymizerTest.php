<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests;

use DateTimeImmutable;
use ElGigi\HarParser\Anonymizer;
use ElGigi\HarParser\Entities\Content;
use ElGigi\HarParser\Entities\Cookie;
use ElGigi\HarParser\Entities\Header;
use ElGigi\HarParser\Entities\Log;
use ElGigi\HarParser\Entities\PostData;
use ElGigi\HarParser\Entities\QueryString;
use ElGigi\HarParser\Parser;
use ElGigi\HarParser\Tests\Fake\FakeEntry;
use ElGigi\HarParser\Tests\Fake\FakeRequest;
use ElGigi\HarParser\Tests\Fake\FakeResponse;
use PHPUnit\Framework\TestCase;

class AnonymizerTest extends TestCase
{
    public function testAnonymizeLog()
    {
        $parser = new Parser();
        $log = $parser->parse(__DIR__ . '/example.har', true);

        $anonymizer = new Anonymizer();
        $anonymized = $anonymizer->anonymize($log);

        $this->assertSame($log->getBrowser(), $anonymized->getBrowser());
        $this->assertSame($log->getCreator(), $anonymized->getCreator());
        $this->assertSame($log->getPages(), $anonymized->getPages());
        $this->assertSame($log->getVersion(), $anonymized->getVersion());
        $this->assertSame(
            count(iterator_to_array($log->getEntries())),
            count(iterator_to_array($anonymized->getEntries())),
        );
    }

    public function testAnonymizeEntry()
    {
        $anonymizer = new Anonymizer();
        $entry = new FakeEntry(
            startedDateTime: $startedDateTime = new DateTimeImmutable(),
            request: new FakeRequest(
                cookies: [
                    new Cookie('foo', 'value', null, null, null, null),
                ],
                headers: [
                    new Header('Name', 'value'),
                    new Header('Cookie', 'qux=value'),
                ],
            ),
            response: new FakeResponse(
                cookies: [
                    new Cookie('foo', 'value', null, null, null, null),
                ],
                headers: [
                    new Header('Name', 'value'),
                    new Header('X-Key', 'value'),
                ],
            )
        );

        $this->assertEquals(
            [
                'startedDateTime' => $startedDateTime->format(Log::DATE_FORMAT),
                'time' => 123.0,
                'request' => [
                    'method' => 'GET',
                    'url' => 'https://getberlioz.com',
                    'httpVersion' => '1.1',
                    'cookies' => [
                        [
                            'name' => 'foo',
                            'value' => 'redacted',
                        ],
                    ],
                    'headers' => [
                        [
                            'name' => 'Name',
                            'value' => 'value',
                        ],
                        [
                            'name' => 'Cookie',
                            'value' => 'redacted',
                        ],
                    ],
                    'queryString' => [],
                    'headersSize' => 0,
                    'bodySize' => 0,
                ],
                'response' => [
                    'status' => 200,
                    'statusText' => 'OK',
                    'httpVersion' => '1.1',
                    'cookies' => [
                        [
                            'name' => 'foo',
                            'value' => 'redacted',
                        ],
                    ],
                    'headers' => [
                        [
                            'name' => 'Name',
                            'value' => 'value',
                        ],
                        [
                            'name' => 'X-Key',
                            'value' => 'redacted',
                        ],
                    ],
                    'content' => [
                        'size' => 0,
                        'mimeType' => 'application/octet-stream',
                    ],
                    'redirectURL' => '',
                    'headersSize' => 0,
                    'bodySize' => 0,
                ],
                'cache' => [],
                'timings' => [
                    'send' => 123.0,
                    'wait' => 123.0,
                    'receive' => 123.0,
                ],
            ],
            $anonymizer->anonymizeEntry($entry)->getArrayCopy(),
        );
    }

    public function testAnonymizeRequest()
    {
        $anonymizer = new Anonymizer();

        $request = new FakeRequest(
            cookies: [
                new Cookie('foo', 'value', null, null, null, null),
                new Cookie('bar', 'value', null, null, null, null),
            ],
            headers: [
                new Header('Name', 'value'),
                new Header('X-Key', 'value'),
                new Header('Cookie', 'qux=value'),
            ],
        );

        $this->assertEquals(
            [
                'method' => 'GET',
                'url' => 'https://getberlioz.com',
                'httpVersion' => '1.1',
                'cookies' => [
                    [
                        'name' => 'foo',
                        'value' => 'redacted',
                    ],
                    [
                        'name' => 'bar',
                        'value' => 'redacted',
                    ],
                ],
                'headers' => [
                    [
                        'name' => 'Name',
                        'value' => 'value',
                    ],
                    [
                        'name' => 'X-Key',
                        'value' => 'redacted',
                    ],
                    [
                        'name' => 'Cookie',
                        'value' => 'redacted',
                    ],
                ],
                'queryString' => [],
                'headersSize' => 0,
                'bodySize' => 0,
            ],
            $anonymizer->anonymizeRequest($request)->getArrayCopy(),
        );
    }

    public function testAnonymizeResponse()
    {
        $anonymizer = new Anonymizer();

        $response = new FakeResponse(
            cookies: [
                new Cookie('foo', 'value', null, null, null, null),
                new Cookie('bar', 'value', null, null, null, null),
            ],
            headers: [
                new Header('Name', 'value'),
                new Header('X-Key', 'value'),
                new Header('Set-Cookie', 'qux=value'),
            ],
        );

        $this->assertEquals(
            [
                'status' => 200,
                'statusText' => 'OK',
                'httpVersion' => '1.1',
                'cookies' => [
                    [
                        'name' => 'foo',
                        'value' => 'redacted',
                    ],
                    [
                        'name' => 'bar',
                        'value' => 'redacted',
                    ],
                ],
                'headers' => [
                    [
                        'name' => 'Name',
                        'value' => 'value',
                    ],
                    [
                        'name' => 'X-Key',
                        'value' => 'redacted',
                    ],
                    [
                        'name' => 'Set-Cookie',
                        'value' => 'redacted',
                    ],
                ],
                'content' => [
                    'size' => 0,
                    'mimeType' => 'application/octet-stream',
                ],
                'redirectURL' => '',
                'headersSize' => 0,
                'bodySize' => 0,
            ],
            $anonymizer->anonymizeResponse($response)->getArrayCopy(),
        );
    }

    public function testAnonymizeHeader()
    {
        $anonymizer = new Anonymizer();

        $header = new Header(
            name: 'My-Header',
            value: 'value'
        );
        $this->assertEquals(
            [
                'name' => 'My-Header',
                'value' => 'value'
            ],
            $anonymizer->anonymizeHeader($header)->getArrayCopy()
        );

        $header = new Header(
            name: 'My-Key-Header',
            value: 'value'
        );
        $this->assertEquals(
            [
                'name' => 'My-Key-Header',
                'value' => 'redacted'
            ],
            $anonymizer->anonymizeHeader($header)->getArrayCopy()
        );
    }

    public function testAnonymizeQueryString()
    {
        $anonymizer = new Anonymizer();

        $queryString = new QueryString(
            name: 'name',
            value: 'value'
        );
        $this->assertEquals(
            [
                'name' => 'name',
                'value' => 'value'
            ],
            $anonymizer->anonymizeQueryString($queryString)->getArrayCopy()
        );

        $queryString = new QueryString(
            name: 'session_id',
            value: 'value'
        );
        $this->assertEquals(
            [
                'name' => 'session_id',
                'value' => 'redacted'
            ],
            $anonymizer->anonymizeQueryString($queryString)->getArrayCopy()
        );
    }

    public function testAnonymizeCookie()
    {
        $anonymizer = new Anonymizer();

        $cookie = new Cookie(
            name: 'myCookie',
            value: 'value',
            path: null,
            domain: null,
            expires: null,
            httpOnly: null
        );
        $this->assertEquals(
            [
                'name' => 'myCookie',
                'value' => 'redacted',
            ],
            $anonymizer->anonymizeCookie($cookie)->getArrayCopy()
        );
    }

    public function testAnonymizePostData()
    {
        $anonymizer = new Anonymizer();
        $anonymizer->addContentToRedact(['text' => 'anon']);

        $postData = new PostData(
            mimeType: 'application/json',
            params: [],
            text: 'Fake text to anonymize'
        );
        $this->assertEquals(
            [
                'mimeType' => 'application/json',
                'text' => 'redacted'
            ],
            $anonymizer->anonymizePostData($postData)->getArrayCopy()
        );
    }

    public function testAnonymizeContent()
    {
        $anonymizer = new Anonymizer();
        $anonymizer->addContentToRedact(['/text/i' => '**']);

        $content = new Content(
            size: 22,
            compression: null,
            mimeType: 'text/html',
            text: 'Fake text to anonymize'
        );
        $this->assertEquals(
            [
                'size' => 20,
                'mimeType' => 'text/html',
                'text' => 'Fake ** to anonymize'
            ],
            $anonymizer->anonymizeContent($content)->getArrayCopy()
        );

        $content = new Content(
            size: 22,
            compression: null,
            mimeType: 'application/pdf',
            text: 'Fake text to anonymize'
        );
        $this->assertSame(
            $content,
            $anonymizer->anonymizeContent($content)
        );
    }
}
