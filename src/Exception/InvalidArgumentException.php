<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2021 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

declare(strict_types=1);

namespace ElGigi\HarParser\Exception;

use ElGigi\HarParser\Entities\EntityInterface;

class InvalidArgumentException extends HarFileException
{
    /**
     * Missing argument.
     *
     * @param string $name
     * @param EntityInterface|string $entity
     *
     * @return static
     */
    public static function missing(string $name, EntityInterface|string $entity): static
    {
        return new static(
            sprintf(
                'Missing "%s" argument for "%s" entity',
                $name,
                is_string($entity) ? $entity : $entity::class
            )
        );
    }
}