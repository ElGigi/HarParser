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
use ElGigi\HarParser\Entities\Page;

interface BuilderInterface
{
    /**
     * Reset.
     *
     * @return void
     */
    public function reset(): void;

    /**
     * Set version.
     *
     * @param string $version
     *
     * @return void
     */
    public function setVersion(string $version): void;

    /**
     * Set creator.
     *
     * @param Creator $creator
     *
     * @return void
     */
    public function setCreator(Creator $creator): void;

    /**
     * Set browser.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function setBrowser(Browser $browser): void;

    /**
     * Add page.
     *
     * @param Page ...$page
     *
     * @return void
     */
    public function addPage(Page ...$page): void;

    /**
     * Add entry.
     *
     * @param Entry ...$entry
     *
     * @return void
     */
    public function addEntry(Entry ...$entry): void;

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return void
     */
    public function setComment(?string $comment): void;
}