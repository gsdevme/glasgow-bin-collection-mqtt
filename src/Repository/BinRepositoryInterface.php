<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Repository;

use Gsdev\GlasgowWasteCollection\Model\Bins;

interface BinRepositoryInterface
{
    public function findAll(int $id): Bins;
}
