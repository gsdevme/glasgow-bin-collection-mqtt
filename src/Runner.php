<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection;

use Gsdev\GlasgowWasteCollection\Publish\MqttPublisher;
use Gsdev\GlasgowWasteCollection\Repository\GoutteBinRepository;
use Gsdev\GlasgowWasteCollection\Service\FetchAndStoreBinCollectionDays;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class Runner
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function run(): void
    {
        $logger = new ConsoleLogger($this->output);

        $mqttHost = strval(getenv('MQTT_HOST'));

        if (empty($mqttHost)) {
            $this->output->writeln('MQTT_HOST ENV not set');

            exit(1);
        }

        $id = intval(getenv('GLASGOW_COUNCIL_ID'));

        if (empty($id) || $id <= 0) {
            $this->output->writeln('GLASGOW_COUNCIL_ID ENV not set or not a number');

            exit(1);
        }

        $fetchAndStore = new FetchAndStoreBinCollectionDays(
            new GoutteBinRepository($logger),
            new MqttPublisher($mqttHost),
            $logger
        );
        $result = $fetchAndStore($id);

        if (!$result) {
            $this->output->writeln('Failed to publish bin days.');

            exit(1);
        }

        $this->output->writeln('Published bin days.');

        exit(0);
    }
}
