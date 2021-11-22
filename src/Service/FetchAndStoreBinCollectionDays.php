<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Service;

use Gsdev\GlasgowWasteCollection\Publish\PublisherInterface;
use Gsdev\GlasgowWasteCollection\Repository\BinRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class FetchAndStoreBinCollectionDays
{
    private BinRepositoryInterface $binRepository;

    private PublisherInterface $publisher;

    private LoggerInterface $logger;

    public function __construct(
        BinRepositoryInterface $binRepository,
        PublisherInterface $publisher,
        ?LoggerInterface $logger = null
    ) {
        $this->binRepository = $binRepository;
        $this->publisher = $publisher;
        $this->logger = $logger ?: new NullLogger();
    }

    public function __invoke(int $id): bool
    {
        $bins = $this->binRepository->findAll($id);

        if (!$bins->hasExpectedAmountOfBins()) {
            $this->logger->error(sprintf('Unexpected number of bins returned, got %d', $bins->count()));

            return false;
        }

        $this->publisher->publish($bins);

        return true;
    }
}
