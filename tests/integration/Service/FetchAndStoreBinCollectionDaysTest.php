<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Tests\Integration\Service;

use Gsdev\GlasgowWasteCollection\Model\Bin;
use Gsdev\GlasgowWasteCollection\Model\Bins;
use Gsdev\GlasgowWasteCollection\Publish\PublisherInterface;
use Gsdev\GlasgowWasteCollection\Repository\GoutteBinRepository;
use Gsdev\GlasgowWasteCollection\Service\FetchAndStoreBinCollectionDays;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\VarDumper;
use function PHPStan\dumpType;

class FetchAndStoreBinCollectionDaysTest extends TestCase
{
    private const LOCAL = 'localhost:3391';

    private Process $server;

    protected function setUp(): void
    {
        $fixtures = sprintf('%s/fixtures/', realpath(__DIR__));

        $this->server = new Process([
            'php',
            '-S',
            self::LOCAL,
            '-t',
            $fixtures,
        ]);
    }

    protected function tearDown(): void
    {
        $this->server->stop();
    }

    public function testWithoutMqtt(): void
    {
        $this->server->start();

        $logger = new NullLogger();

        sleep(2);

        $c = function (Bins $bins) {
            $this->assertEquals(
                '[{"name":"Blue","cycle":14,"date":"2021-12-03"},{"name":"Brown","cycle":14,"date":"2021-11-26"},{"name":"Green","cycle":21,"date":"2021-12-10"},{"name":"Purple","cycle":56,"date":"2022-01-04"}]',
                json_encode($bins)
            );
        };

        $fakeMqtt = new class($c) implements PublisherInterface {
            private $c;

            public function __construct(callable $c)
            {
                $this->c = $c;
            }

            public function publish(Bins $bins): void
            {
                ($this->c)($bins);
            }
        };

        (new FetchAndStoreBinCollectionDays(
            new GoutteBinRepository($logger, sprintf('http://%s', self::LOCAL)),
            $fakeMqtt
        ))(
            111
        );
    }

}
