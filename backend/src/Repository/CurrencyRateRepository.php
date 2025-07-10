<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function findRatesByDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('cr')
            ->select('cr.charCode', 'cr.name', 'cr.rate')
            ->andWhere('cr.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    public function findLatestDate(): ?\DateTimeInterface
{
    $result = $this->createQueryBuilder('cr')
        ->select('MAX(cr.date) as max_date')
        ->getQuery()
        ->getSingleScalarResult();

    return $result ? new \DateTime($result) : null;
}

}
