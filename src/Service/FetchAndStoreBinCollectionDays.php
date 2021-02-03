<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Service;

use Gsdev\GlasgowWasteCollection\Publish\PublisherInterface;
use Gsdev\GlasgowWasteCollection\Repository\BinRepositoryInterface;

class FetchAndStoreBinCollectionDays
{
    private BinRepositoryInterface $binRepository;

    private PublisherInterface $publisher;

    public function __construct(BinRepositoryInterface $binRepository, PublisherInterface $publisher)
    {
        $this->binRepository = $binRepository;
        $this->publisher = $publisher;
    }

    public function __invoke(int $id): bool
    {
        $bins = $this->binRepository->findAll($id);

        if (!$bins->hasExpectedAmountOfBins()) {
            return false;
        }

        $this->publisher->publish($bins);

        return true;
    }
}
