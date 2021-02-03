<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Publish;

use Gsdev\GlasgowWasteCollection\Model\Bin;
use Gsdev\GlasgowWasteCollection\Model\Bins;
use PhpMqtt\Client\MqttClient;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MqttPublisher implements PublisherInterface
{
    private const CLIENT_ID = 'Gsdev-GlasgowWasteCollection';
    private const TOPIC = 'gcc/bins/%s';

    private MqttClient $client;

    private LoggerInterface $logger;

    public function __construct(string $host, int $port = 1883, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();

        $this->client = new MqttClient($host, $port, self::CLIENT_ID, MqttClient::MQTT_3_1, null, $this->logger);
    }

    public function publish(Bins $bins): void
    {
        if (!$this->client->isConnected()) {
            $this->client->connect();
        }

        foreach ($bins as $bin) {
            if (!$bin instanceof Bin) {
                continue;
            }

            try {
                $payload = json_encode($bin);

                if (!is_string($payload)) {
                    throw new \JsonException('Invalid');
                }
            } catch (\JsonException $e) {
                $this->logger->error(sprintf('JSON payload for the bin failed to form: %s', $e->getMessage()));

                continue;
            }

            $this->client->publish(strtolower(sprintf(self::TOPIC, $bin->getName())), $payload, 0, true);
        }

        $this->client->disconnect();
    }
}
