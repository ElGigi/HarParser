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

use DateTimeImmutable;
use ElGigi\HarParser\Entities\Entry;
use ElGigi\HarParser\Entities\Request;
use ElGigi\HarParser\Entities\Response;
use ElGigi\HarParser\Entities\Timings;

class FakeEntry extends Entry
{
    public function __construct(
        ?string $pageref = null,
        ?DateTimeImmutable $startedDateTime = null,
        ?float $time = null,
        ?Request $request = null,
        ?Response $response = null,
        ?array $cache = null,
        ?Timings $timings = null,
        ?string $serverIPAddress = null,
        ?string $connection = null,
        ?string $comment = null
    ) {
        parent::__construct(
            pageref: $pageref,
            startedDateTime: $startedDateTime ?? new DateTimeImmutable(),
            time: $time ?? 123,
            request: $request ?? new FakeRequest(),
            response: $response ?? new FakeResponse(),
            cache: $cache ?? [],
            timings: $timings ?? new Timings(
                blocked: null,
                dns: null,
                connect: null,
                send: 123,
                wait: 123,
                receive: 123,
                ssl: null,
            ),
            serverIPAddress: $serverIPAddress,
            connection: $connection,
            comment: $comment,
        );
    }
}