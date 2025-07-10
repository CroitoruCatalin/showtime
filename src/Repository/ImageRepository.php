<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function create(UploadedFile $file, $targetDir, $fileName): Image
    {
        $file->move($targetDir, $fileName);

        $image = (new Image())
            ->setFilename($fileName)
            ->setDateTimeCreated(new \DateTime('now'));
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush();

        return $image;
    }

    public function save(Image $image, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($image);
        if ($flush) {
            $em->flush();
        }
    }

    public function delete(Image $image, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($image);
        if ($flush) {
            $em->flush();
        }
    }

    //    /**
    //     * @return Image[] Returns an array of Image objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Image
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
