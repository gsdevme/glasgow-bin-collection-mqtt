<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Model;

class Bin implements \JsonSerializable
{
    private string $name;

    private int $cycle;

    private \DateTimeInterface $nextDate;

    public function __construct(string $name, int $cycle, \DateTimeInterface $nextDate)
    {
        $this->name = $name;
        $this->cycle = $cycle;
        $this->nextDate = $nextDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'cycle' => $this->cycle,
            'date' => $this->nextDate->format('Y-m-d'),
        ];
    }
}
