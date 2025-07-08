<?php

namespace App\Repository;

use App\Entity\Band;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Band>
 */
class BandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Band::class);
    }

    public function findFilteredPaginatedSorted(array $criteria, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('b');

        if (!empty($criteria['name_starts'])) {
            $qb->andWhere('b.name LIKE :name_starts')
                ->setParameter('name_starts', $criteria['name_starts'] . '%');
        }

        if (!empty($criteria['genre'])) {
            $qb->andWhere('b.genre LIKE :genre')
                ->setParameter('genre', $criteria['genre']);
        }

        $validSortCriteria = ['id', 'name', 'genre'];

        $sort = isset($criteria['sort']) && in_array($criteria['sort'], $validSortCriteria, true) ? $criteria['sort'] : 'id';
        $dir = (isset($criteria['direction']) && strtoupper($criteria['direction']) == 'DESC') ? 'DESC' : 'ASC';
        $qb->orderBy("b.$sort", $dir);

        $total = (clone $qb)
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
        
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $items = $qb->getQuery()->getResult();
        return [
            'items' => $items,
            'total' => $total,
        ];
    }
//    /**
//     * @return Band[] Returns an array of Band objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Band
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
