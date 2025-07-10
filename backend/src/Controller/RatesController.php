<?php

namespace App\Controller;

use App\Repository\CurrencyRateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RatesController extends AbstractController
{
    #[Route('/api/rates', name: 'api_rates', methods: ['GET'])]
        public function getRates(CurrencyRateRepository $repository, CacheInterface $cache): JsonResponse
    {
        $data = $cache->get('currency_rates_list', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(3600); 

            $latestDate = $repository->findLatestDate();

            if (!$latestDate) {
                return []; 
            }

            $previousDate = (clone $latestDate)->modify('-1 day');

            $latestRates = $repository->findRatesByDate($latestDate);
            $previousRates = $repository->findRatesByDate($previousDate);

            $previousRatesMap = [];
            foreach ($previousRates as $rate) {
                $previousRatesMap[$rate['charCode']] = $rate['rate'];
            }

            $result = [];
            foreach ($latestRates as $rate) {
                $previousRate = $previousRatesMap[$rate['charCode']] ?? null;
                $diff = ($previousRate !== null) ? ($rate['rate'] - $previousRate) : 0;

                $result[] = [
                    'char_code' => $rate['charCode'],
                    'name' => $rate['name'],
                    'rate' => $rate['rate'],
                    'diff' => round($diff, 4),
                ];
            }
            return $result;
        });

        return $this->json($data);
    }
}