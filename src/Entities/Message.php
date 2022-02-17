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

abstract class Message
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
}