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

use ElGigi\HarParser\Entities\Header;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    public function loadProvider(): array
    {
        return [
            [
                'data' => [
                    'name' => 'Foo',
                    'value' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expected' => [
                    'name' => 'Foo',
                    'value' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'name' => 'Foo',
                    'value' => 'Bar',
                ],
                'expected' => [
                    'name' => 'Foo',
                    'value' => 'Bar',
                ],
                'expectedException' => null,
                'expectedExceptionMessage' => null,
            ],
            [
                'data' => [
                    'value' => 'Bar',
                    'comment' => 'Baz',
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => InvalidArgumentException::missing('name', Header::class)->getMessage(),
            ],
            [
                'data' => [
                    'name' => 'Foo',
                    'comment' => 'Baz',
                ],
                'expected' => null,
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => InvalidArgumentException::missing(
                    'value',
                    Header::class
                )->getMessage(),
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

        $entity = Header::load($data);
        $this->assertEquals($expected, $entity->getArrayCopy());
    }

    public function testGetArrayCopy()
    {
        $entity = new Header(name: 'Foo', value: 'Bar');

        $this->assertEquals(
            [
                'name' => 'Foo',
                'value' => 'Bar',
            ],
            $entity->getArrayCopy()
        );

        $entity = new Header(name: 'Foo', value: 'Bar', comment: 'Baz');

        $this->assertEquals(
            [
                'name' => 'Foo',
                'value' => 'Bar',
                'comment' => 'Baz',
            ],
            $entity->getArrayCopy()
        );
    }

    public function testJsonSerialize()
    {
        $entity = new Header(name: 'Foo', value: 'Bar');

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());

        $entity = new Header(name: 'Foo', value: 'Bar', comment: 'Baz');

        $this->assertEquals($entity->getArrayCopy(), $entity->jsonSerialize());
    }

    public function testGetName()
    {
        $entity = new Header(name: 'Foo', value: 'Bar');

        $this->assertEquals('Foo', $entity->getName());
    }

    public function testGetValue()
    {
        $entity = new Header(name: 'Foo', value: 'Bar');

        $this->assertEquals('Bar', $entity->getValue());
    }

    public function testGetComment()
    {
        $entity = new Header(name: 'Foo', value: 'Bar');

        $this->assertNull($entity->getComment());

        $entity = new Header(name: 'Foo', value: 'Bar', comment: 'Baz');

        $this->assertEquals('Baz', $entity->getComment());
    }
}
