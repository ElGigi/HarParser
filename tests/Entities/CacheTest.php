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

use DateTime;
use ElGigi\HarParser\Entities\Cache;
use ElGigi\HarParser\Entities\Log;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function loadProvider(): array
    {
        return [
            [
                'data' => [
                    'expires' => null,
                    'lastAccess' => null,
                    'eTag' => 'Foo',
                    'hitCount' => 3,
                    'comment' => 'Comment',
                ],
                'expected' => [
                    'eTag' => 'Foo',
                    'hitCount' => 3,
                    'comment' => 'Comment',
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'lastAccess' => '2022-02-18 00:00:00 +00:00',
                    'eTag' => 'Foo',
                    'hitCount' => 3,
                ],
                'expected' => [
                    'lastAccess' => (new DateTime('2022-02-18T00:00:00.000+00:00'))->format(Log::DATE_FORMAT),
                    'eTag' => 'Foo',
                    'hitCount' => 3,
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'hitCount' => 3,
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => InvalidArgumentException::missing('eTag', Cache::class)->getMessage(),
            ],
            [
                'data' => [
                    'eTag' => 'Foo',
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => InvalidArgumentException::missing('hitCount', Cache::class)->getMessage(),
            ],
            [
                'data' => [
                    'expires' => 'BAD DATE',
                    'eTag' => 'Foo',
                    'hitCount' => 3,
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => 'Invalid argument',
            ]
        ];
    }

    /**
     * @dataProvider loadProvider
     */
    public function testLoad(
        array $data,
        ?array $expected = null,
        ?string $expectedException = null,
        ?string $expectedExceptionMessage = null
    ) {
        if (null !== $expectedException) {
            $this->expectException($expectedException);

            if (null !== $expectedExceptionMessage) {
                $this->expectExceptionMessage($expectedExceptionMessage);
            }
        }

        $entity = Cache::load($data);
        $this->assertEquals($expected, $entity->getArrayCopy());
    }

    public function testJsonSerialize()
    {
        $entity = new Cache(
            expires: $date = new DateTime(),
            lastAccess: $date,
            eTag: 'Foo',
            hitCount: 3,
            comment: 'Comment'
        );

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());

        $entity = new Cache(
            expires: null,
            lastAccess: null,
            eTag: 'Foo',
            hitCount: 3,
            comment: 'Comment'
        );

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());
    }

    public function testGetArrayCopy()
    {
        $entity = new Cache(
            expires: $date = new DateTime(),
            lastAccess: $date,
            eTag: 'Foo',
            hitCount: 3,
            comment: 'Comment'
        );

        $this->assertEquals(
            [
                'expires' => $date->format(Log::DATE_FORMAT),
                'lastAccess' => $date->format(Log::DATE_FORMAT),
                'eTag' => 'Foo',
                'hitCount' => 3,
                'comment' => 'Comment',
            ],
            $entity->getArrayCopy()
        );

        $entity = new Cache(
            expires: null,
            lastAccess: null,
            eTag: 'Foo',
            hitCount: 3,
            comment: 'Comment'
        );

        $this->assertEquals(
            [
                'eTag' => 'Foo',
                'hitCount' => 3,
                'comment' => 'Comment',
            ],
            $entity->getArrayCopy()
        );
    }

    public function testGetExpires()
    {
        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertNull($entity->getExpires());

        $entity = new Cache(expires: $expected = new DateTime(), lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertEquals($expected, $entity->getExpires());
    }

    public function testGetLastAccess()
    {
        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertNull($entity->getLastAccess());

        $entity = new Cache(expires: null, lastAccess: $expected = new DateTime(), eTag: 'Foo', hitCount: 3);

        $this->assertEquals($expected, $entity->getLastAccess());
    }

    public function testGetETag()
    {
        $entity = new Cache(expires: null, lastAccess: null, eTag: '', hitCount: 3);

        $this->assertEquals('', $entity->getETag());

        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertEquals('Foo', $entity->getETag());
    }

    public function testGetHitCount()
    {
        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertEquals(3, $entity->getHitCount());
    }

    public function testGetComment()
    {
        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3);

        $this->assertNull($entity->getComment());

        $entity = new Cache(expires: null, lastAccess: null, eTag: 'Foo', hitCount: 3, comment: 'Baz');

        $this->assertEquals('Baz', $entity->getComment());
    }
}
