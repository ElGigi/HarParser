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

namespace ElGigi\HarParser;

use ElGigi\HarParser\Entities\Log;

/**
 * Class HarParser.
 */
class Parser
{
    public function __construct()
    {
    }

    /**
     * Parse HAR content.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function parse(string|array $content, bool $contentIsFile = false): Log
    {
        if (true === $contentIsFile) {
            $content = file_get_contents($content);
        }

        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        return Log::load($content);
    }

    /**
     * Clear HAR.
     *
     * @param array $data
     * @return array
     */
    public function clear(array $data): array
    {
        $final = [];

        foreach ($data as $key => $value) {
            if (str_starts_with((string)$key, '_')) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->clear($value);
            }

            $final[$key] = $value;
        }

        return $final;
    }
}