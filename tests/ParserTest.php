<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2021 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests;

use ElGigi\HarParser\Parser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ParserTest extends TestCase
{
    public function testParse()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/example.har'), true);
        $parser = new Parser();
        $log = $parser->parse($json);

        $this->assertCount(count($json['log']['entries']), iterator_to_array($log->getEntries()));
        $this->assertEquals($parser->clear($json), json_decode(json_encode($log), true));
    }

    public function testParse_file()
    {
        $parser = new Parser();
        $log = $parser->parse(__DIR__ . '/example.har', true);

        $this->assertCount(61, iterator_to_array($log->getEntries()));
    }

    public function testParse_fileNotFound()
    {
        $this->expectException(RuntimeException::class);

        $parser = new Parser();
        $parser->parse(__DIR__ . '/fake.har', true);
    }

    public function testClear()
    {
        $parser = new Parser();

        $this->assertEquals(
            [
                'log' => [
                    'request' => [
                        'foo' => 'bar',
                    ],
                ]
            ],
            $parser->clear([
                'log' => [
                    'request' => [
                        'foo' => 'bar',
                        '_baz' => 'qux'
                    ],
                    '_notSpec' => 'test'
                ]
            ])
        );
    }
}
