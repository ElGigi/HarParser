<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests\Entities;

use DateTimeImmutable;
use ElGigi\HarParser\Entities\Browser;
use ElGigi\HarParser\Entities\Creator;
use ElGigi\HarParser\Entities\Log;
use ElGigi\HarParser\Entities\Page;
use ElGigi\HarParser\Entities\PageTimings;
use ElGigi\HarParser\Tests\Fake\FakeEntry;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testGetVersion()
    {
        $entity = new Log(
            version: $expected = 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: []
        );

        $this->assertEquals($expected, $entity->getVersion());
    }

    public function testGetCreator()
    {
        $entity = new Log(
            version: 'Foo',
            creator: $expected = new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: []
        );

        $this->assertEquals($expected, $entity->getCreator());
    }

    public function testGetBrowser()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: []
        );

        $this->assertNull($entity->getBrowser());

        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: $expected = new Browser('Berlioz', 'v2.0'),
            pages: [],
            entries: []
        );

        $this->assertEquals($expected, $entity->getBrowser());
    }

    public function testGetPages()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: $expected = [],
            entries: []
        );

        $this->assertSame($expected, $entity->getPages());

        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: $expected = [
                new Page(
                    startedDateTime: new DateTimeImmutable(),
                    id: 'ID1',
                    title: 'TITLE1',
                    pageTimings: new PageTimings(),
                ),
                new Page(
                    startedDateTime: new DateTimeImmutable(),
                    id: 'ID2',
                    title: 'TITLE2',
                    pageTimings: new PageTimings(),
                ),
            ],
            entries: []
        );

        $this->assertSame($expected, $entity->getPages());
    }

    public function testGetPage()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [
                $page1 = new Page(
                    startedDateTime: new DateTimeImmutable(),
                    id: 'ID1',
                    title: 'TITLE1',
                    pageTimings: new PageTimings(),
                ),
                $page2 = new Page(
                    startedDateTime: new DateTimeImmutable(),
                    id: 'ID2',
                    title: 'TITLE2',
                    pageTimings: new PageTimings(),
                ),
            ],
            entries: []
        );

        $this->assertNull($entity->getPage('UNKNOWN'));
        $this->assertSame($page1, $entity->getPage('ID1'));
        $this->assertSame($page2, $entity->getPage('ID2'));
    }

    public function testAddPage()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: []
        );

        $entity->addPage(
            $page1 = new Page(
                startedDateTime: new DateTimeImmutable(),
                id: 'ID1',
                title: 'TITLE1',
                pageTimings: new PageTimings(),
            ),
            $page2 = new Page(
                startedDateTime: new DateTimeImmutable(),
                id: 'ID2',
                title: 'TITLE2',
                pageTimings: new PageTimings(),
            ),
        );

        $this->assertSame([$page1, $page2], $entity->getPages());
    }

    public function testGetEntries()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: $expected = []
        );

        $this->assertSame($expected, iterator_to_array($entity->getEntries()));

        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: $expected = [
                new FakeEntry(),
                new FakeEntry(),
            ]
        );

        $this->assertSame($expected, iterator_to_array($entity->getEntries()));
    }

    public function testGetEntry()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [
                $page = new Page(
                    startedDateTime: new DateTimeImmutable(),
                    id: 'ID',
                    title: 'TITLE',
                    pageTimings: new PageTimings(),
                ),
            ],
            entries: [
                $entry1 = new FakeEntry(),
                $entry2 = new FakeEntry(pageref: 'ID'),
            ]
        );

        $this->assertNull($entity->getEntry(999));
        $this->assertSame($entry1, $entity->getEntry(0));
        $this->assertSame($entry2, $entity->getEntry(1));
        $this->assertSame($entry2, $entity->getEntry(0, 'ID'));
        $this->assertSame($entry2, $entity->getEntry(0, $page));
    }

    public function testAddEntry()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: $expected = []
        );

        $this->assertSame($expected, iterator_to_array($entity->getEntries()));

        $entity->addEntry(
            $entry1 = new FakeEntry(),
            $entry2 = new FakeEntry(pageref: 'ID'),
        );

        $this->assertSame([$entry1, $entry2], iterator_to_array($entity->getEntries()));
    }

    public function testGetComment()
    {
        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: []
        );

        $this->assertNull($entity->getComment());

        $entity = new Log(
            version: 'Foo',
            creator: new Creator('Berlioz', 'v2.0'),
            browser: null,
            pages: [],
            entries: [],
            comment: $expected = 'Comment'
        );

        $this->assertEquals($expected, $entity->getComment());
    }
}
