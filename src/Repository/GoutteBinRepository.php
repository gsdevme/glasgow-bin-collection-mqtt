<?php

declare(strict_types=1);

namespace Gsdev\GlasgowWasteCollection\Repository;

use Goutte\Client;
use Gsdev\GlasgowWasteCollection\Model\Bin;
use Gsdev\GlasgowWasteCollection\Model\Bins;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class GoutteBinRepository implements BinRepositoryInterface
{
    private const URL = 'https://www.glasgow.gov.uk/forms/refuseandrecyclingcalendar/CollectionsCalendar.aspx?UPRN=%d';

    private LoggerInterface $logger;

    private string $url;

    public function __construct(?LoggerInterface $logger = null, ?string $url = null)
    {
        $this->url = $url ?: self::URL;
        $this->logger = $logger ?: new NullLogger();
    }

    public function findAll(int $id): Bins
    {
        try {
            $crawler = $this->getClient()->request('GET', sprintf($this->url, $id));
            $crawler = $crawler->filter('.Fieldset > ul > li');

            if ($crawler->count() <=> Bins::EXPECTED_NUMBER_OF_BINS) {
                $this->logger->error(
                    'Request for bins returned incorrect number of expected bins or none at all',
                );

                return new Bins();
            }
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf('HTTP Request to glasgow.gov.uk failed with: %s', $e->getMessage()),
                [
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return new Bins();
        }

        $bins = $crawler->each(
            static function (Crawler $node) {
                switch (true) {
                    case stripos($node->text(), 'Tomorrow'):
                        $bin = sscanf(
                            $node->text(),
                            "Your next %s Bin day is Tomorrow. %s Bins are emptied every %d days."
                        );

                        if (!is_array($bin) || count($bin) !== 3) {
                            return false;
                        }

                        $colour = strval($bin[0]);

                        $date = new \DateTime('tomorrow', new \DateTimeZone('Europe/London'));
                        $cycle = intval($bin[2]);

                        break;
                    default:
                        $bin = sscanf(
                            $node->text(),
                            "Your next %s Bin day is %s %s %s %d. %s Bins are emptied every %d days"
                        );

                        if (!is_array($bin) || count($bin) === 6) {
                            return false;
                        }

                        $colour = strval($bin[0]);

                        $date = \DateTime::createFromFormat(
                            'l jS F Y G:i',
                            sprintf('%s %s %s %d 08:00', $bin[1], $bin[2], $bin[3], $bin[4]),
                            new \DateTimeZone('Europe/London')
                        );
                        $cycle = intval($bin[6]);
                        break;
                }

                if (!$date instanceof \DateTimeInterface) {
                    return false;
                }

                return new Bin($colour, $cycle, $date);
            }
        );

        if (count($bins) <= 0) {
            return new Bins();
        }

        return new Bins(...array_filter($bins));
    }

    private function getClient(): Client
    {
        return new Client(HttpClient::create(['timeout' => 10]));
    }
}
