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

namespace ElGigi\HarParser\Entities;

use ElGigi\HarParser\Exception\InvalidArgumentException;
use JsonSerializable;

/**
 * Class Header.
 */
class Header implements JsonSerializable
{
    public function __construct(
        protected string $name,
        protected string $value,
        protected ?string $comment = null,
    ) {
    }

    /**
     * Load.
     *
     * @param array $data
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public static function load(array $data): static
    {
        return new static(
            name: $data['name'] ?? throw InvalidArgumentException::missing('log.entries[].(request|response)[].headers[].name'),
            value: $data['value'] ?? throw InvalidArgumentException::missing('log.entries[].(request|response)[].headers[].value'),
            comment: $data['comment'] ?? null,
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'value' => $this->value,
                'comment' => $this->comment,
            ],
            fn($value) => null !== $value
        );
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}