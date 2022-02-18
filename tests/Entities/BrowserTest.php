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

use ElGigi\HarParser\Entities\Browser;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
    public function loadProvider(): array
    {
        return [
            [
                'data' => [
                    'name' => 'Foo',
                    'version' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expected' => [
                    'name' => 'Foo',
                    'version' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'name' => 'Foo',
                    'version' => 'Bar',
                ],
                'expected' => [
                    'name' => 'Foo',
                    'version' => 'Bar',
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'version' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => InvalidArgumentException::missing('name', Browser::class)->getMessage(),
            ],
            [
                'data' => [
                    'name' => 'Foo',
                    'comment' => 'Baz',
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' =>
                    InvalidArgumentException::missing('version', Browser::class)->getMessage(),
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

        $entity = Browser::load($data);
        $this->assertEquals($expected, $entity->getArrayCopy());
    }

    public function testGetArrayCopy()
    {
        $entity = new Browser(name: 'Foo', version: 'Bar');

        $this->assertEquals(
            [
                'name' => 'Foo',
                'version' => 'Bar',
            ],
            $entity->getArrayCopy()
        );

        $entity = new Browser(name: 'Foo', version: 'Bar', comment: 'Baz');

        $this->assertEquals(
            [
                'name' => 'Foo',
                'version' => 'Bar',
                'comment' => 'Baz',
            ],
            $entity->getArrayCopy()
        );
    }

    public function testJsonSerialize()
    {
        $entity = new Browser(name: 'Foo', version: 'Bar');

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());

        $entity = new Browser(name: 'Foo', version: 'Bar', comment: 'Baz');

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());
    }

    public function testGetName()
    {
        $entity = new Browser(name: 'Foo', version: 'Bar');

        $this->assertEquals('Foo', $entity->getName());
    }

    public function testgetVersion()
    {
        $entity = new Browser(name: 'Foo', version: 'Bar');

        $this->assertEquals('Bar', $entity->getVersion());
    }

    public function testGetComment()
    {
        $entity = new Browser(name: 'Foo', version: 'Bar');

        $this->assertNull($entity->getComment());

        $entity = new Browser(name: 'Foo', version: 'Bar', comment: 'Baz');

        $this->assertEquals('Baz', $entity->getComment());
    }
}
