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

class Response extends Message
{
    public function __construct(
        protected int $status,
        protected string $statusText,
        string $httpVersion,
        array $cookies,
        array $headers,
        protected Content $content,
        protected string $redirectURL,
        int $headersSize,
        int $bodySize,
        ?string $comment = null,
    )
    {
        parent::__construct(
            httpVersion: $httpVersion,
            cookies: $cookies,
            headers: $headers,
            headersSize: $headersSize,
            bodySize: $bodySize,
            comment: $comment
        );
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
            $cookies = array_map(fn($cookie) => Cookie::load($cookie), $data['cookies'] ?? []);
            $headers = array_map(fn($header) => Header::load($header), $data['headers'] ?? []);

            return new static(
                status: $data['status'] ?? throw InvalidArgumentException::missing('log.entries[].response.status'),
                statusText: $data['statusText'] ?? throw InvalidArgumentException::missing('log.entries[].response.statusText'),
                httpVersion: $data['httpVersion'] ?? throw InvalidArgumentException::missing('log.entries[].response.httpVersion'),
                cookies: $cookies,
                headers: $headers,
                content: Content::load($data['content'] ?? []),
                redirectURL: $data['redirectURL'] ?? throw InvalidArgumentException::missing('log.entries[].response.redirectURL'),
                headersSize: $data['headersSize'] ?? throw InvalidArgumentException::missing('log.entries[].response.headersSize'),
                bodySize: $data['bodySize'] ?? throw InvalidArgumentException::missing('log.entries[].response.bodySize'),
                comment: $data['comment'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            var_dump($data);
            throw $exception;
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
                'status' => $this->status,
                'statusText' => $this->statusText,
                'httpVersion' => $this->httpVersion,
                'cookies' => array_map(fn(Cookie $cookie) => $cookie->getArrayCopy(), $this->cookies),
                'headers' => array_map(fn(Header $header) => $header->getArrayCopy(), $this->headers),
                'content' => $this->content->getArrayCopy(),
                'redirectURL' => $this->redirectURL,
                'headersSize' => $this->headersSize,
                'bodySize' => $this->bodySize,
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
     * Get status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get status text.
     *
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->statusText;
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
     * Get content.
     *
     * @return Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectURL;
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