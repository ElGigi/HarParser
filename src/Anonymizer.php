<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser;

use ElGigi\HarParser\Entities\Content;
use ElGigi\HarParser\Entities\Cookie;
use ElGigi\HarParser\Entities\EntityInterface;
use ElGigi\HarParser\Entities\Entry;
use ElGigi\HarParser\Entities\Header;
use ElGigi\HarParser\Entities\Log;
use ElGigi\HarParser\Entities\Param;
use ElGigi\HarParser\Entities\PostData;
use ElGigi\HarParser\Entities\QueryString;
use ElGigi\HarParser\Entities\Request;
use ElGigi\HarParser\Entities\Response;
use ElGigi\HarParser\Exception\InvalidArgumentException;

class Anonymizer
{
    protected const REDACTED_TEXT = 'redacted';

    protected array $headers = [
        'authorization',
        '.*session.*',
        '.*token.*',
        '.*key.*',
        '.*client.*',
        '.*secret.*',
        '.*signature.*',
        'cookie',
        'set-cookie',
    ];
    protected array $queryString = [
        '.*session.*',
        '.*token.*',
        'key',
        '.*secret.*',
        'signature',
        'user(_?name)?',
        'password',
    ];
    protected array $postData = [
        '.*session.*',
        '.*token.*',
        'key',
        '.*secret.*',
        'signature',
        'user(_?name)?',
        'password',
    ];
    protected array $mimes = [
        'text/',
        'application/json',
    ];
    protected array $contents = [];
    protected array $callbacks = [];

    /**
     * Add header to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addHeaderToRedact(string ...$regex): void
    {
        array_push($this->headers, ...$regex);
    }

    /**
     * Add query string to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addQueryStringToRedact(string ...$regex): void
    {
        array_push($this->queryString, ...$regex);
    }

    /**
     * Add post data to redact.
     *
     * @param string ...$regex
     *
     * @return void
     */
    public function addPostDataToRedact(string ...$regex): void
    {
        array_push($this->postData, ...$regex);
    }

    /**
     * Add accepted mime.
     *
     * @param string ...$mime
     *
     * @return void
     */
    public function addAcceptedMime(string ...$mime): void
    {
        array_push($this->mimes, ...$mime);
    }

    /**
     * Add content to redact.
     *
     * @param array $contents
     *
     * @return void
     */
    public function addContentToRedact(array $contents): void
    {
        $this->contents = array_replace($this->contents, $contents);
    }

    /**
     * Add callback.
     *
     * @param callable ...$callback
     *
     * @return void
     */
    public function addCallback(callable ...$callback): void
    {
        array_push($this->callbacks, ...$callback);
    }

    /**
     * Call callback.
     *
     * @template T
     * @param T $entity
     *
     * @return T
     */
    protected function callCallback(EntityInterface $entity): EntityInterface
    {
        foreach ($this->callbacks as $callback) {
            $entity = $callback($entity);
        }

        return $entity;
    }

    /**
     * Anonymize HAR file.
     *
     * @param Log $log
     *
     * @return Log
     * @throws InvalidArgumentException
     */
    public function anonymize(Log $log): Log
    {
        $log = new Log(
            version: $log->getVersion(),
            creator: $log->getCreator(),
            browser: $log->getBrowser(),
            pages: $log->getPages(),
            entries: array_map(
                fn(Entry $entry) => $this->anonymizeEntry($entry),
                iterator_to_array($log->getEntries()),
            ),
            comment: $log->getComment(),
        );

        return $this->callCallback($log);
    }

    /**
     * Anonymize entry.
     *
     * @param Entry $entry
     *
     * @return Entry
     * @throws InvalidArgumentException
     */
    public function anonymizeEntry(Entry $entry): Entry
    {
        $entry = new Entry(
            pageref: $entry->getPageref(),
            startedDateTime: $entry->getStartedDateTime(),
            time: $entry->getTime(),
            request: $this->anonymizeRequest($entry->getRequest()),
            response: $this->anonymizeResponse($entry->getResponse()),
            cache: $entry->getCache(),
            timings: $entry->getTimings(),
            serverIPAddress: $entry->getServerIPAddress(),
            connection: $entry->getConnection(),
            comment: $entry->getComment(),
        );

        return $this->callCallback($entry);
    }

    /**
     * Anonymize request.
     *
     * @param Request $request
     *
     * @return Request
     * @throws InvalidArgumentException
     */
    public function anonymizeRequest(Request $request): Request
    {
        $adjustHeadersSize = 0;
        $adjustBodySize = 0;

        $request = new Request(
            method: $request->getMethod(),
            url: $request->getUrl(),
            httpVersion: $request->getHttpVersion(),
            cookies: array_map(
                fn(Cookie $cookie) => $this->anonymizeCookie($cookie, $adjustHeadersSize),
                $request->getCookies(),
            ),
            headers: array_map(
                fn(Header $header) => $this->anonymizeHeader($header, $adjustHeadersSize),
                $request->getHeaders(),
            ),
            queryString: array_map(
                fn(QueryString $queryString) => $this->anonymizeQueryString($queryString),
                $request->getQueryString(),
            ),
            postData: $this->anonymizePostData($request->getPostData(), $adjustBodySize),
            headersSize: ($size = $request->getHeadersSize()) > 0 ? $size : $size + $adjustHeadersSize,
            bodySize: ($size = $request->getBodySize()) > 0 ? $size : $size + $adjustBodySize,
            comment: $request->getComment(),
        );

        return $this->callCallback($request);
    }

    /**
     * Anonymize response.
     *
     * @param Response $response
     *
     * @return Response
     * @throws InvalidArgumentException
     */
    public function anonymizeResponse(Response $response): Response
    {
        $adjustSize = 0;
        $adjustBodySize = 0;

        $response = new Response(
            status: $response->getStatus(),
            statusText: $response->getStatusText(),
            httpVersion: $response->getHttpVersion(),
            cookies: array_map(
                fn(Cookie $cookie) => $this->anonymizeCookie($cookie, $adjustSize),
                $response->getCookies(),
            ),
            headers: array_map(
                fn(Header $header) => $this->anonymizeHeader($header, $adjustSize),
                $response->getHeaders(),
            ),
            content: $this->anonymizeContent($response->getContent(), $adjustBodySize),
            redirectURL: $response->getRedirectUrl(),
            headersSize: $response->getHeadersSize() > 0 ? -1 : $response->getHeadersSize() + $adjustSize,
            bodySize: ($size = $response->getBodySize()) > 0 ? $size : $size + $adjustBodySize,
            comment: $response->getComment(),
        );

        return $this->callCallback($response);
    }

    /**
     * Anonymize header.
     *
     * @param Header $header
     * @param int $adjustSize
     *
     * @return Header
     */
    public function anonymizeHeader(Header $header, int &$adjustSize = 0): Header
    {
        foreach ($this->headers as $headerToRedact) {
            if (1 !== preg_match(sprintf('/^%s$/i', $headerToRedact), $header->getName())) {
                continue;
            }

            $adjustSize = strlen(static::REDACTED_TEXT) - strlen($header->getValue());

            $header = new Header(
                name: $header->getName(),
                value: static::REDACTED_TEXT,
                comment: $header->getComment(),
            );
            break;
        }

        return $this->callCallback($header);
    }

    /**
     * Anonymize query string.
     *
     * @param QueryString $queryString
     * @param int $adjustSize
     *
     * @return QueryString
     */
    public function anonymizeQueryString(QueryString $queryString, int &$adjustSize = 0): QueryString
    {
        foreach ($this->queryString as $queryStringToRedact) {
            if (1 !== preg_match(sprintf('/^%s$/i', $queryStringToRedact), $queryString->getName())) {
                continue;
            }

            $adjustSize = strlen(static::REDACTED_TEXT) - strlen($queryString->getValue());

            $queryString = new QueryString(
                name: $queryString->getName(),
                value: static::REDACTED_TEXT,
                comment: $queryString->getComment(),
            );
            break;
        }

        return $this->callCallback($queryString);
    }

    /**
     * Anonymize post data.
     *
     * @param PostData|null $postData
     * @param int $adjustSize
     *
     * @return PostData|null
     * @throws InvalidArgumentException
     */
    public function anonymizePostData(?PostData $postData, int &$adjustSize = 0): ?PostData
    {
        if (null === $postData) {
            return null;
        }

        $text = $postData->getText();
        if (is_resource($text)) {
            $text = stream_get_contents($text, offset: 0);
        }

        if (!empty($text)) {
            $adjustSize = strlen(self::REDACTED_TEXT) - strlen($text);
            $text = self::REDACTED_TEXT;
        }

        $postData = new PostData(
            mimeType: $postData->getMimeType(),
            params: array_map(
                fn(Param $param) => $this->anonymizePostDataParam($param),
                $postData->getParams(),
            ),
            text: $text,
            comment: $postData->getComment(),
        );

        return $this->callCallback($postData);
    }

    /**
     * Anonymize post data param.
     *
     * @param Param $param
     *
     * @return Param
     */
    public function anonymizePostDataParam(Param $param): Param
    {
        foreach ($this->postData as $paramToRedact) {
            if (1 !== preg_match(sprintf('/^%s$/i', $paramToRedact), $param->getName())) {
                continue;
            }

            $param = new Param(
                name: $param->getName(),
                value: static::REDACTED_TEXT,
                fileName: $param->getFileName(),
                contentType: $param->getContentType(),
                comment: $param->getComment(),
            );
            break;
        }

        return $this->callCallback($param);
    }

    /**
     * Anonymize cookie.
     *
     * @param Cookie $cookie
     * @param int $adjustSize
     *
     * @return Cookie
     */
    public function anonymizeCookie(Cookie $cookie, int &$adjustSize = 0): Cookie
    {
        $adjustSize = strlen(static::REDACTED_TEXT) - strlen($cookie->getValue());

        $cookie = new Cookie(
            name: $cookie->getName(),
            value: static::REDACTED_TEXT,
            path: $cookie->getPath(),
            domain: $cookie->getDomain(),
            expires: $cookie->getExpires(),
            httpOnly: $cookie->isHttpOnly(),
            secure: $cookie->isSecure(),
            sameSite: $cookie->getSameSite(),
            comment: $cookie->getComment(),
        );

        return $this->callCallback($cookie);
    }

    /**
     * Anonymize content.
     *
     * @param Content $content
     * @param int $adjustSize
     *
     * @return Content
     * @throws InvalidArgumentException
     */
    public function anonymizeContent(Content $content, int &$adjustSize = 0): Content
    {
        foreach ($this->mimes as $mime) {
            if (!str_starts_with($content->getMimeType(), $mime)) {
                continue;
            }

            if (null !== $content->getEncoding() && $content->getEncoding() !== 'base64') {
                return $content;
            }

            $adjustSize = 0;

            $text = $content->getText();
            if (is_resource($text)) {
                $text = stream_get_contents($text, offset: 0);
            }

            if ($content->getEncoding() === 'base64') {
                $text = base64_decode($text);
            }

            $length = strlen($text);
            $text = str_replace(array_keys($this->contents), array_values($this->contents), $text);
            $adjustSize = strlen($text) - $length;

            if ($content->getEncoding() === 'base64') {
                $text = base64_encode($text);
            }

            $content = new Content(
                size: strlen($text),
                compression: $content->getCompression(),
                mimeType: $content->getMimeType(),
                text: $text,
                encoding: $content->getEncoding(),
                comment: $content->getComment(),
            );
            break;
        }

        return $this->callCallback($content);
    }
}