<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Model;

class Bins implements \JsonSerializable, \Countable, \IteratorAggregate
{
    public const EXPECTED_NUMBER_OF_BINS = 4;

    /**
     * @var array<int, Bin>
     */
    private array $bins;

    public function __construct(Bin ...$bins)
    {
        $this->bins = $bins;
    }

    public function hasExpectedAmountOfBins(): bool
    {
        return $this->count() === self::EXPECTED_NUMBER_OF_BINS;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->bins);
    }

    public function count(): int
    {
        return count($this->bins);
    }

    public function jsonSerialize()
    {
        return $this->bins;
    }
}
