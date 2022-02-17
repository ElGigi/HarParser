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

use DateTimeImmutable;
use DateTimeInterface;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use Exception;
use JsonSerializable;

class Cache implements JsonSerializable
{
    public function __construct(
        protected ?DateTimeInterface $expires,
        protected ?DateTimeInterface $lastAccess,
        protected string $eTag,
        protected int $hitCount,
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
        try {
            return new static(
                expires: $data['expires'] ? new DateTimeImmutable($data['expires']) : null,
                lastAccess: $data['lastAccess'] ? new DateTimeImmutable($data['lastAccess']) : null,
                eTag: $data['eTag'] ?? throw InvalidArgumentException::missing('log.entries[].cache[].eTag'),
                hitCount: $data['hitCount'] ??
                throw InvalidArgumentException::missing('log.entries[].cache[].hitCount'),
                comment: $data['comment'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new InvalidArgumentException('Invalid argument', previous: $exception);
        }
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
                'expires' => $this->expires?->format(Log::DATE_FORMAT),
                'lastAccess' => $this->lastAccess?->format(Log::DATE_FORMAT),
                'eTag' => $this->eTag,
                'hitCount' => $this->hitCount,
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
     * Get expires.
     *
     * @return DateTimeInterface|null
     */
    public function getExpires(): ?DateTimeInterface
    {
        return $this->expires;
    }

    /**
     * Get last access.
     *
     * @return DateTimeInterface|null
     */
    public function getLastAccess(): ?DateTimeInterface
    {
        return $this->lastAccess;
    }

    /**
     * Get eTag.
     *
     * @return string
     */
    public function getETag(): string
    {
        return $this->eTag;
    }

    /**
     * Get hit count.
     *
     * @return int
     */
    public function getHitCount(): int
    {
        return $this->hitCount;
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