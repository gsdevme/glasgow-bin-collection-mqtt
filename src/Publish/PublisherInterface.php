<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Publish;

use Gsdev\GlasgowWasteCollection\Model\Bins;

interface PublisherInterface
{
    public function publish(Bins $bins): void;
}
