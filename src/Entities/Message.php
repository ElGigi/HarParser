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

abstract class Message implements EntityInterface
{
    public function __construct(
        protected string $httpVersion,
        protected array $cookies,
        protected array $headers,
        protected int $headersSize,
        protected int $bodySize,
        protected ?string $comment = null,
    ) {
        $this->cookies = array_filter($this->cookies, fn($cookie) => $cookie instanceof Cookie);
        $this->headers = array_filter($this->headers, fn($header) => $header instanceof Header);
    }

    /**
     * Get HTTP version.
     *
     * @return string
     */
    public function getHttpVersion(): string
    {
        return $this->httpVersion;
    }

    /**
     * Get cookies.
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get headers size.
     *
     * @return int
     */
    public function getHeadersSize(): int
    {
        return $this->headersSize;
    }

    /**
     * Get body size.
     *
     * @return int
     */
    public function getBodySize(): int
    {
        return $this->bodySize;
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