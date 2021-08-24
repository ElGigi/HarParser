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
 * Class PostData.
 */
class PostData implements JsonSerializable
{
    public function __construct(
        protected string $mimeType,
        protected array $params,
        protected string $text,
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
        $params = array_map(fn($param) => Param::load($param), $data['params'] ?? []);

        return new static(
            mimeType: $data['mimeType'] ?? throw InvalidArgumentException::missing('log.entries[].request[].postData[].mimeType'),
            params: $params,
            text: $data['text'] ?? throw InvalidArgumentException::missing('log.entries[].request[].postData[].text'),
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
                'mimeType' => $this->mimeType,
                'params' => $this->params ?: null,
                'text' => $this->text,
                'comment' => $this->comment,
            ],
            fn($value) => null !== $value
        );
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
     * Get params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
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