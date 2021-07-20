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
 * Class Timing.
 */
class Timings implements JsonSerializable
{
    public function __construct(
        protected ?float $blocked,
        protected ?float $dns,
        protected ?float $connect,
        protected float $send,
        protected float $wait,
        protected float $receive,
        protected ?float $ssl,
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
                blocked: $data['blocked'] ?? null,
                dns: $data['dns'] ?? null,
                connect: $data['connect'] ?? null,
                send: $data['send'] ?? throw InvalidArgumentException::missing('log.entries[].timing.send'),
                wait: $data['wait'] ?? throw InvalidArgumentException::missing('log.entries[].timing.wait'),
                receive: $data['receive'] ?? throw InvalidArgumentException::missing('log.entries[].timing.receive'),
                ssl: $data['ssl'] ?? null,
                comment: $data['comment'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'blocked' => $this->blocked,
                'dns' => $this->dns,
                'connect' => $this->connect,
                'send' => $this->send,
                'wait' => $this->wait,
                'receive' => $this->receive,
                'ssl' => $this->ssl,
                'comment' => $this->comment,
            ],
            fn($value) => null !== $value
        );
    }

    /**
     * Get blocked.
     *
     * @return float|null
     */
    public function getBlocked(): ?float
    {
        return $this->blocked;
    }

    /**
     * Get DNS.
     *
     * @return float|null
     */
    public function getDns(): ?float
    {
        return $this->dns;
    }

    /**
     * Get connect.
     *
     * @return float|null
     */
    public function getConnect(): ?float
    {
        return $this->connect;
    }

    /**
     * Get send.
     *
     * @return float
     */
    public function getSend(): float
    {
        return $this->send;
    }

    /**
     * Get wait.
     *
     * @return float
     */
    public function getWait(): float
    {
        return $this->wait;
    }

    /**
     * Get receive.
     *
     * @return float
     */
    public function getReceive(): float
    {
        return $this->receive;
    }

    /**
     * Get SSL.
     *
     * @return float|null
     */
    public function getSsl(): ?float
    {
        return $this->ssl;
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