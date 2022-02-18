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

class FakeContent extends Content
{
    public function __construct(
        ?int $size = null,
        ?int $compression = null,
        ?string $mimeType = null,
        ?string $text = null,
        ?string $encoding = null,
        ?string $comment = null
    ) {
        parent::__construct(
            size: $size ?? 0,
            compression: $compression,
            mimeType: $mimeType ?? 'application/octet-stream',
            text: $text,
            encoding: $encoding,
            comment: $comment,
        );
    }
}