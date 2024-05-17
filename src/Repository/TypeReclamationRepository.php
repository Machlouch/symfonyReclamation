<?php

namespace App\Repository;

use App\Entity\TypeReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeReclamation>
 *
 * @method TypeReclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeReclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeReclamation[]    findAll()
 * @method TypeReclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeReclamation::class);
    }

    public function save(TypeReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findBySortedField($sortField, $sortDirection): array
    {
        // 'findBy' takes criteria, orderBy, limit, and offset. We are using orderBy here.
        return $this->findBy([], [$sortField => $sortDirection]);
    }   
    public function findByNom($nom)
    {
        return $this->createQueryBuilder('tr')
            ->andWhere('tr.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getResult(); // Change this line to getResult()
    }
    public function getPercentageByType()
    {
        $total = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $query = $this->createQueryBuilder('t')
            ->select('t.nom', 'COUNT(t.id) as count')
            ->groupBy('t.nom')
            ->getQuery()
            ->getResult();

        $percentages = [];
        foreach ($query as $result) {
            $percentage = ($result['count'] / $total) * 100;
            $percentages[$result['nom']] = $percentage;
        }

        return $percentages;
    }
    
    



//    /**
//     * @return TypeReclamation[] Returns an array of TypeReclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeReclamation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
