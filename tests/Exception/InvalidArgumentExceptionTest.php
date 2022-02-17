<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests\Exception;

use ElGigi\HarParser\Exception\HarFileException;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testMissing()
    {
        $exception = InvalidArgumentException::missing('FOO', 'BAR');

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(HarFileException::class, $exception);
        $this->assertEquals('Missing "FOO" argument for "BAR" entity', $exception->getMessage());
    }
}
