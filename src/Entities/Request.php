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

class Request extends Message implements JsonSerializable
{
    public function __construct(
        protected string $method,
        protected string $url,
        string $httpVersion,
        array $cookies,
        array $headers,
        protected array $queryString,
        protected ?PostData $postData,
        int $headersSize,
        int $bodySize,
        ?string $comment = null,
    ) {
        parent::__construct(
            httpVersion: $httpVersion,
            cookies: $cookies,
            headers: $headers,
            headersSize: $headersSize,
            bodySize: $bodySize,
            comment: $comment
        );

        $this->queryString = array_filter($this->queryString, fn($queryString) => $queryString instanceof QueryString);
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
        $cookies = array_map(fn($cookie) => Cookie::load($cookie), $data['cookies'] ?? []);
        $headers = array_map(fn($header) => Header::load($header), $data['headers'] ?? []);
        $queryString = array_map(fn($query) => QueryString::load($query), $data['queryString'] ?? []);

        return new static(
            method: $data['method'] ?? throw InvalidArgumentException::missing('log.entries[].request.method'),
            url: $data['url'] ?? throw InvalidArgumentException::missing('log.entries[].request.url'),
            httpVersion: $data['httpVersion'] ?? throw InvalidArgumentException::missing(
                'log.entries[].request.httpVersion'
            ),
            cookies: $cookies,
            headers: $headers,
            queryString: $queryString,
            postData: isset($data['postData']) ? PostData::load($data['postData']) : null,
            headersSize: $data['headersSize'] ?? throw InvalidArgumentException::missing(
                'log.entries[].request.headersSize'
            ),
            bodySize: $data['bodySize'] ?? throw InvalidArgumentException::missing('log.entries[].request.bodySize'),
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
                'method' => $this->method,
                'url' => $this->url,
                'httpVersion' => $this->httpVersion,
                'cookies' => array_map(fn(Cookie $cookie) => $cookie->getArrayCopy(), $this->cookies),
                'headers' => array_map(fn(Header $header) => $header->getArrayCopy(), $this->headers),
                'queryString' => array_map(fn(QueryString $query) => $query->getArrayCopy(), $this->queryString),
                'postData' => $this->postData?->getArrayCopy() ?: null,
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
     * Get method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
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
     * Get query string.
     *
     * @return array
     */
    public function getQueryString(): array
    {
        return $this->queryString;
    }

    /**
     * Get post data.
     *
     * @return PostData|null
     */
    public function getPostData(): ?PostData
    {
        return $this->postData;
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