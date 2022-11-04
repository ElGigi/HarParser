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
use ElGigi\HarParser\Exception\InvalidArgumentException;
use Exception;

class Entry implements EntityInterface
{
    public function __construct(
        protected ?string $pageref,
        protected DateTimeImmutable $startedDateTime,
        protected float $time,
        protected Request $request,
        protected ?Response $response,
        protected array $cache,
        protected Timings $timings,
        protected ?string $serverIPAddress,
        protected ?string $connection = null,
        protected ?string $comment = null,
    ) {
        // Set bad response
        $this->response ??= new Response(
            status: 0,
            statusText: '',
            httpVersion: '',
            cookies: [],
            headers: [],
            content: new Content(
                size: 0,
                compression: null,
                mimeType: 'x-unknown',
                text: '',
            ),
            redirectURL: '',
            headersSize: -1,
            bodySize: -1,
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
            $cache = array_map(fn($aCache) => Cache::load($aCache), $data['cache'] ?? []);

            return new static(
                pageref: $data['pageref'] ?? null,
                startedDateTime: new DateTimeImmutable(
                    $data['startedDateTime'] ??
                    throw InvalidArgumentException::missing('startedDateTime', static::class)
                ),
                time: $data['time'] ?? throw InvalidArgumentException::missing('time', static::class),
                request: Request::load(
                    $data['request'] ??
                    throw InvalidArgumentException::missing('request', static::class)
                ),
                response: Response::load(
                    $data['response'] ??
                    throw InvalidArgumentException::missing('response', static::class)
                ),
                cache: $cache,
                timings: Timings::load($data['timings'] ?? []),
                serverIPAddress: $data['serverIPAddress'] ?? null,
                connection: $data['connection'] ?? null,
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
                'pageref' => $this->pageref,
                'startedDateTime' => $this->startedDateTime->format(Log::DATE_FORMAT),
                'time' => $this->time,
                'request' => $this->request->getArrayCopy(),
                'response' => $this->response->getArrayCopy(),
                'cache' => array_map(fn(Cache $cache) => $cache->getArrayCopy(), $this->cache),
                'timings' => $this->timings->getArrayCopy(),
                'serverIPAddress' => $this->serverIPAddress,
                'connection' => $this->connection,
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
     * Get page reference.
     *
     * @return string|null
     */
    public function getPageref(): ?string
    {
        return $this->pageref;
    }

    /**
     * Get started date time.
     *
     * @return DateTimeImmutable
     */
    public function getStartedDateTime(): DateTimeImmutable
    {
        return $this->startedDateTime;
    }

    /**
     * Get time.
     *
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * Get request.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get response.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get cache.
     *
     * @return array
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * Get timings.
     *
     * @return Timings
     */
    public function getTimings(): Timings
    {
        return $this->timings;
    }

    /**
     * Get server IP address.
     *
     * @return string|null
     */
    public function getServerIPAddress(): ?string
    {
        return $this->serverIPAddress;
    }

    /**
     * Get connection.
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return $this->connection;
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