<?php

namespace App\Repository;

use App\Entity\RecResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecResponse>
 *
 * @method RecResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecResponse[]    findAll()
 * @method RecResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecResponse::class);
    }
    public function findAllQuery(): \Doctrine\ORM\QueryBuilder
{
    return $this->createQueryBuilder('r')
        ->orderBy('r.id', 'DESC');
}

//    /**
//     * @return RecResponse[] Returns an array of RecResponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RecResponse
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
