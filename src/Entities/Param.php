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
 * Class Param.
 */
class Param implements JsonSerializable
{
    public function __construct(
        protected string $name,
        protected ?string $value,
        protected ?string $fileName,
        protected ?string $contentType,
        protected ?string $comment = null,
    )
    {
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
            name: $data['name'] ?? throw InvalidArgumentException::missing('log.entries[].request[].postData[].params[].name'),
            value: $data['value'] ?? null,
            fileName: $data['fileName'] ?? null,
            contentType: $data['contentType'] ?? null,
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
                'fileName' => $this->fileName,
                'contentType' => $this->contentType,
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
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Get file name.
     *
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Get content type.
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
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