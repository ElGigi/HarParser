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

class PageTimings implements EntityInterface
{
    public function __construct(
        protected ?float $onContentLoad,
        protected ?float $onLoad,
        protected ?string $comment,
    ) {
    }

    /**
     * Load.
     *
     * @param array $data
     *
     * @return static
     */
    public static function load(array $data): static
    {
        return new static(
            onContentLoad: $data['onContentLoad'] ?? null,
            onLoad: $data['onLoad'] ?? null,
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
        return array_filter(
            [
                'onContentLoad' => $this->onContentLoad,
                'onLoad' => $this->onLoad,
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
     * Get on content load.
     *
     * @return float|null
     */
    public function getOnContentLoad(): ?float
    {
        return $this->onContentLoad;
    }

    /**
     * Get on load.
     *
     * @return float|null
     */
    public function getOnLoad(): ?float
    {
        return $this->onLoad;
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