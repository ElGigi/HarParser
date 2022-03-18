<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Builder;

use ElGigi\HarParser\Entities\Browser;
use ElGigi\HarParser\Entities\Creator;
use ElGigi\HarParser\Entities\Entry;
use ElGigi\HarParser\Entities\Page;
use InvalidArgumentException;
use RuntimeException;

class BuilderStream implements BuilderInterface
{
    private const SECTION_VERSION = 0;
    private const SECTION_CREATOR = 1;
    private const SECTION_BROWSER = 2;
    private const SECTION_PAGES = 3;
    private const SECTION_ENTRIES = 4;
    private const SECTION_COMMENT = 5;
    private array $positions;

    public function __construct(private $fp)
    {
        if (!is_resource($fp)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument #1 must be a valid resource, actual %s',
                    get_debug_type($fp)
                )
            );
        }

        $this->reset();
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        if (false === ftruncate($this->fp, 0)) {
            throw new RuntimeException('Unable to truncate resource');
        }

        $defaultData = '{"log":{"version":1.2,"creator":null,"pages":[],"entries":[]}}';

        if (false === fwrite($this->fp, $defaultData)) {
            throw new RuntimeException('Unable to write initial data on resource');
        }

        $this->positions = [
            self::SECTION_VERSION => [18, 3],
            self::SECTION_CREATOR => [32, 4],
            self::SECTION_BROWSER => [36, 0],
            self::SECTION_PAGES => [46, 0],
            self::SECTION_ENTRIES => [59, 0],
            self::SECTION_COMMENT => [60, 0],
        ];
    }

    /**
     * Write on stream.
     *
     * @param string $section
     * @param string $data
     * @param bool $concat
     * @param string|null $separator
     *
     * @return void
     */
    protected function write(string $section, string $data, bool $concat, ?string $separator = null): void
    {
        if (true === $concat) {
            // Remove separator if no data
            if ($this->positions[$section][1] === 0) {
                $separator = null;
            }

            $data = ($separator ?? '') . $data;
            $dataLength = $written = strlen($data);

            if ($dataLength > 0) {
                $written = b_fwritei(
                    $this->fp,
                    $data,
                    length: $dataLength,
                    offset: array_sum($this->positions[$section])
                );
            }

            if (false === $written) {
                throw new RuntimeException('Unable to write on resource');
            }

            $this->positions[$section][1] += $dataLength;
            $this->shiftSection($section, $dataLength);
            return;
        }

        b_ftruncate($this->fp, $this->positions[$section][1], $this->positions[$section][0]);

        $this->shiftSection($section, -$this->positions[$section][1]);
        $this->positions[$section][1] = 0;

        $this->write($section, $data, true);
    }

    /**
     * Shift sections.
     *
     * @param string $section
     * @param int $length
     *
     * @return void
     */
    protected function shiftSection(string $section, int $length): void
    {
        foreach ($this->positions as $i => &$position) {
            if ($i > $section) {
                $position[0] += $length;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function setVersion(string $version): void
    {
        $this->write(self::SECTION_VERSION, json_encode($version), false);
    }

    /**
     * @inheritDoc
     */
    public function setCreator(Creator $creator): void
    {
        $this->write(self::SECTION_CREATOR, json_encode($creator), false);
    }

    /**
     * @inheritDoc
     */
    public function setBrowser(?Browser $browser): void
    {
        if (null === $browser) {
            $this->write(self::SECTION_BROWSER, '', false);
            return;
        }

        $this->write(self::SECTION_BROWSER, ',"browser":' . json_encode($browser), false);
    }

    /**
     * @inheritDoc
     */
    public function addPage(Page ...$page): void
    {
        foreach ($page as $item) {
            $this->write(self::SECTION_PAGES, json_encode($item), true, ',');
        }
    }

    /**
     * @inheritDoc
     */
    public function addEntry(Entry ...$entry): void
    {
        foreach ($entry as $item) {
            $this->write(self::SECTION_ENTRIES, json_encode($item), true, ',');
        }
    }

    /**
     * @inheritDoc
     */
    public function setComment(?string $comment): void
    {
        if (null === $comment) {
            $this->write(self::SECTION_COMMENT, '', false);
            return;
        }

        $this->write(self::SECTION_COMMENT, ',"comment":' . json_encode($comment), false);
    }
}