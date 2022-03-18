<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

declare(strict_types=1);

namespace ElGigi\HarParser\Builder;

use ElGigi\HarParser\Entities\Browser;
use ElGigi\HarParser\Entities\Creator;
use ElGigi\HarParser\Entities\Entry;
use ElGigi\HarParser\Entities\Log;
use ElGigi\HarParser\Entities\Page;
use RuntimeException;

class Builder implements BuilderInterface
{
    private string $version = '1.2';
    private ?Creator $creator;
    private ?Browser $browser;
    private array $pages;
    private array $entries;
    private ?string $comment;

    public function __construct(?Log $log = null)
    {
        $this->reset();

        if (null !== $log) {
            $this->creator = $log->getCreator();
            $this->browser = $log->getBrowser();
            $this->pages = $log->getPages() ?? [];
            $this->entries = iterator_to_array($log->getEntries(), false);
            $this->comment = $log->getComment();
        }
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        $this->version = '1.2';
        $this->creator = null;
        $this->browser = null;
        $this->pages = [];
        $this->entries = [];
        $this->comment = null;
    }

    /**
     * @inheritDoc
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @inheritDoc
     */
    public function setCreator(Creator $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @inheritDoc
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @inheritDoc
     */
    public function addPage(Page ...$page): void
    {
        array_push($this->pages, ...$page);
    }

    /**
     * @inheritDoc
     */
    public function addEntry(Entry ...$entry): void
    {
        array_push($this->entries, ...$entry);
    }

    /**
     * @inheritDoc
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Build.
     *
     * @return Log
     */
    public function build(): Log
    {
        $log = new Log(
            version: $this->version,
            creator: $this->creator ?? throw new RuntimeException('Missing "creator" field'),
            browser: $this->browser ?? throw new RuntimeException('Missing "browser" field'),
            pages: $this->pages,
            entries: $this->entries,
            comment: $this->comment,
        );

        $this->reset();

        return $log;
    }
}