<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests\Fake;

use ElGigi\HarParser\Entities\Content;
use ElGigi\HarParser\Entities\Response;

class FakeResponse extends Response
{
    public function __construct(
        ?int $status = null,
        ?string $statusText = null,
        ?string $httpVersion = null,
        ?array $cookies = null,
        ?array $headers = null,
        ?Content $content = null,
        ?string $redirectURL = null,
        ?int $headersSize = null,
        ?int $bodySize = null,
        ?string $comment = null
    ) {
        parent::__construct(
            status: $status ?? 200,
            statusText: $statusText ?? 'OK',
            httpVersion: $httpVersion ?? '1.1',
            cookies: $cookies ?? [],
            headers: $headers ?? [],
            content: $content ?? new FakeContent(),
            redirectURL: $redirectURL ?? '',
            headersSize: $headersSize ?? 0,
            bodySize: $bodySize ?? 0,
            comment: $comment
        );
    }
}