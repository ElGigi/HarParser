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

use ElGigi\HarParser\Entities\PostData;
use ElGigi\HarParser\Entities\Request;

class FakeRequest extends Request
{
    public function __construct(
        ?string $method = null,
        ?string $url = null,
        ?string $httpVersion = null,
        ?array $cookies = null,
        ?array $headers = null,
        ?array $queryString = null,
        ?PostData $postData = null,
        ?int $headersSize = null,
        ?int $bodySize = null,
        ?string $comment = null
    ) {
        parent::__construct(
            method: $method ?? 'GET',
            url: $url ?? 'https://getberlioz.com',
            httpVersion: $httpVersion ?? '1.1',
            cookies: $cookies ?? [],
            headers: $headers ?? [],
            queryString: $queryString ?? [],
            postData: $postData,
            headersSize: $headersSize ?? 0,
            bodySize: $bodySize ?? 0,
            comment: $comment
        );
    }
}