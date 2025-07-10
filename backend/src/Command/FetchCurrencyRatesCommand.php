<?php

namespace App\Command;

use App\Entity\CurrencyRate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(name: 'app:fetch-rates')]
class FetchCurrencyRatesCommand extends Command
{
    private $entityManager;

    private const CURRENCIES_TO_FETCH = ['USD', 'EUR', 'CNY'];

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://www.cbr.ru/scripts/XML_daily.asp');
        $xmlContent = $response->getContent();

        $xml = new \SimpleXMLElement($xmlContent);
        $date = new \DateTime((string)$xml['Date']);

        foreach ($xml->Valute as $valute) {
            $charCode = (string)$valute->CharCode;

            if (in_array($charCode, self::CURRENCIES_TO_FETCH)) {
                $existingRate = $this->entityManager->getRepository(CurrencyRate::class)->findOneBy([
                    'charCode' => $charCode,
                    'date' => $date
                ]);

                if (!$existingRate) {
                    $rate = new CurrencyRate();
                    $rate->setCharCode($charCode);
                    $rate->setName((string)$valute->Name);
                    $rate->setRate((float)str_replace(',', '.', $valute->Value));
                    $rate->setDate($date);
                    $this->entityManager->persist($rate);
                }
            }
        }

        $this->entityManager->flush();
        $output->writeln('Currency rates fetched successfully for ' . $date->format('Y-m-d'));
        return Command::SUCCESS;
    }
}