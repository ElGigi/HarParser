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

use ElGigi\HarParser\Exception\InvalidArgumentException;
use Generator;

class Log implements EntityInterface
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.vp';

    public function __construct(
        protected string $version,
        protected Creator $creator,
        protected ?Browser $browser,
        protected array $pages,
        protected array $entries,
        protected ?string $comment = null,
    ) {
        $this->pages = array_filter($this->pages, fn($page) => $page instanceof Page);
        $this->entries = array_filter($this->entries, fn($entry) => $entry instanceof Entry);
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
        $pages = array_map(fn($page) => Page::load($page), $data['log']['pages'] ?? []);
        $entries = array_map(fn($entry) => Entry::load($entry), $data['log']['entries'] ?? []);

        return new static(
            version: $data['log']['version'] ?? throw InvalidArgumentException::missing('version', static::class),
            creator: Creator::load(
                $data['log']['creator'] ??
                throw InvalidArgumentException::missing('creator', static::class)
            ),
            browser: isset($data['log']['browser']) ? Browser::load($data['log']['browser']) : null,
            pages: $pages,
            entries: $entries,
            comment: $data['log']['comment'] ?? null,
        );
    }

    /**
     * Get array copy.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'log' => array_filter(
                [
                    'version' => $this->version,
                    'creator' => $this->creator->getArrayCopy(),
                    'browser' => $this->browser->getArrayCopy(),
                    'pages' => array_map(fn(Page $page) => $page->getArrayCopy(), $this->pages) ?: null,
                    'entries' => array_map(fn(Entry $entry) => $entry->getArrayCopy(), $this->entries) ?: null,
                    'comment' => $this->comment,
                ],
                fn($value) => null !== $value
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Get version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get creator.
     *
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * Get browser.
     *
     * @return Browser|null
     */
    public function getBrowser(): ?Browser
    {
        return $this->browser;
    }

    /**
     * Get pages.
     *
     * @return array|null
     */
    public function getPages(): ?array
    {
        return $this->pages;
    }

    /**
     * Get page.
     *
     * @param string $id
     *
     * @return Page|null
     */
    public function getPage(string $id): ?Page
    {
        /** @var Page $page */
        foreach ($this->pages as $page) {
            if ($id === $page->getId()) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Add page.
     *
     * @param Page ...$page
     */
    public function addPage(Page ...$page): void
    {
        array_push($this->pages, ...$page);
    }

    /**
     * Get entries.
     *
     * @param Page|string|null $page
     *
     * @return Generator
     */
    public function getEntries(Page|string|null $page = null): Generator
    {
        if (null === $page) {
            yield from $this->entries;
            return;
        }

        $pageRef = $page instanceof Page ? $page->getId() : $page;

        /** @var Entry $entry */
        foreach ($this->entries as $entry) {
            if ($pageRef === $entry->getPageref()) {
                yield $entry;
            }
        }
    }

    /**
     * Get entry.
     *
     * @param int $entry
     * @param Page|string|null $page
     *
     * @return Entry|null
     */
    public function getEntry(int $entry, Page|string|null $page = null): ?Entry
    {
        if (null === $page) {
            return $this->entries[$entry] ?? null;
        }

        foreach ($this->getEntries($page) as $key => $value) {
            if ($key == $entry) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Add entry.
     *
     * @param Entry ...$entry
     */
    public function addEntry(Entry ...$entry): void
    {
        array_push($this->entries, ...$entry);
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