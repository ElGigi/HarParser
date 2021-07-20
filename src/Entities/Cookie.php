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

/**
 * Class Cookie.
 */
class Cookie implements JsonSerializable
{
    public function __construct(
        protected string $name,
        protected string $value,
        protected ?string $path,
        protected ?string $domain,
        protected ?DateTimeInterface $expires,
        protected ?bool $httpOnly,
        protected ?bool $secure = null,
        protected ?string $sameSite = null,
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
        try {
            return new static(
                name: $data['name'] ?? throw InvalidArgumentException::missing('log.entries[].(request|response)[].cookies[].name'),
                value: $data['value'] ?? throw InvalidArgumentException::missing('log.entries[].(request|response)[].cookies[].value'),
                path: $data['path'] ?? null,
                domain: $data['domain'] ?? null,
                expires: $data['expires'] ? new DateTimeImmutable($data['expires']) : null,
                httpOnly: $data['httpOnly'] ?? null,
                secure: $data['secure'] ?? null,
                sameSite: $data['sameSite'] ?? null,
                comment: $data['comment'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new InvalidArgumentException('Invalid argument', previous: $exception);
        }
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
                'path' => $this->path,
                'domain' => $this->domain,
                'expires' => $this->expires?->format(Log::DATE_FORMAT),
                'httpOnly' => $this->httpOnly,
                'secure' => $this->secure,
                'sameSite' => $this->sameSite,
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
     * Get path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Get domain.
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
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
     * Is HTTP only?
     *
     * @return bool|null
     */
    public function isHttpOnly(): ?bool
    {
        return $this->httpOnly;
    }

    /**
     * Is secure?
     *
     * @return bool|null
     */
    public function isSecure(): ?bool
    {
        return $this->secure;
    }

    /**
     * Get same site.
     *
     * @return string|null
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
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