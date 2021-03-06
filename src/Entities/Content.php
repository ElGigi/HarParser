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

class Content implements EntityInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected int $size,
        protected ?int $compression,
        protected string $mimeType,
        protected $text,
        protected ?string $encoding = null,
        protected ?string $comment = null,
    ) {
        if (!(null === $this->text || is_string($this->text) || is_resource($this->text))) {
            throw new InvalidArgumentException('Argument $text must be null, a string value or a valid resource');
        }
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
            size: $data['size'] ?? throw InvalidArgumentException::missing('size', static::class),
            compression: $data['compression'] ?? null,
            mimeType: $data['mimeType'] ?? throw InvalidArgumentException::missing('mimeType', static::class),
            text: $data['text'] ?? null,
            encoding: $data['encoding'] ?? null,
            comment: $data['comment'] ?? null,
        );
    }

    /**
     * Get array copy.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $text = $this->text;
        $encoding = $this->encoding;

        if (is_resource($text)) {
            $text = base64_encode(stream_get_contents($this->text, -1, 0));
            $encoding = 'base64';
        }

        return array_filter(
            [
                'size' => $this->size,
                'compression' => $this->compression,
                'mimeType' => $this->mimeType,
                'text' => $text,
                'encoding' => $encoding,
                'comment' => $this->comment,
            ],
            fn($value) => null !== $value
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get compression.
     *
     * @return int|null
     */
    public function getCompression(): ?int
    {
        return $this->compression;
    }

    /**
     * Get mime type.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Get text.
     *
     * @return string|resource|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get encoding.
     *
     * @return string|null
     */
    public function getEncoding(): ?string
    {
        return $this->encoding;
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