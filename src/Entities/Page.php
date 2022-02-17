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

use DateTimeImmutable;
use DateTimeInterface;
use ElGigi\HarParser\Exception\InvalidArgumentException;
use Exception;

class Page implements EntityInterface
{
    public function __construct(
        protected DateTimeInterface $startedDateTime,
        protected string $id,
        protected string $title,
        protected PageTimings $pageTimings,
        protected ?string $comment = null,
    )
    {
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
        try {
            return new static(
                startedDateTime: new DateTimeImmutable($data['startedDateTime'] ?? throw InvalidArgumentException::missing('log.pages[].startedDateTime')),
                id: $data['id'] ?? throw InvalidArgumentException::missing('log.pages[].id'),
                title: $data['title'] ?? throw InvalidArgumentException::missing('log.pages[].title'),
                pageTimings: PageTimings::load($data['pageTimings'] ?? throw InvalidArgumentException::missing('log.pages[].pageTimings')),
                comment: $data['comment'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new InvalidArgumentException('Invalid argument', previous: $exception);
        }
    }

    /**
     * Get array copy.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return array_filter(
            [
                'startedDateTime' => $this->startedDateTime->format(Log::DATE_FORMAT),
                'id' => $this->id,
                'title' => $this->title,
                'pageTimings' => $this->pageTimings->getArrayCopy(),
                'comment' => $this->comment,
            ],
            fn($value) => null !== $value
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Get started date time.
     *
     * @return DateTimeInterface
     */
    public function getStartedDateTime(): DateTimeInterface
    {
        return $this->startedDateTime;
    }

    /**
     * Get ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get page timings.
     *
     * @return PageTimings
     */
    public function getPageTimings(): PageTimings
    {
        return $this->pageTimings;
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