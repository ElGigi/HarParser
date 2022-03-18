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

class PostData implements EntityInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected string $mimeType,
        protected array $params,
        protected $text,
        protected ?string $comment = null,
    ) {
        if (!(is_string($this->text) || is_resource($this->text))) {
            throw new InvalidArgumentException('Argument $text must be a string value or a valid resource');
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
        $params = array_map(fn($param) => Param::load($param), $data['params'] ?? []);

        return new static(
            mimeType: $data['mimeType'] ?? throw InvalidArgumentException::missing('mimeType', static::class),
            params: $params,
            text: $data['text'] ?? throw InvalidArgumentException::missing('text', static::class),
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

        if (is_resource($text)) {
            $text = stream_get_contents($this->text, -1, 0);
        }

        return array_filter(
            [
                'mimeType' => $this->mimeType,
                'params' => array_map(fn(Param $param) => $param->getArrayCopy(), $this->params) ?: null,
                'text' => $text,
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
     * @return string|resource
     */
    public function getText()
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