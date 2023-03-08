<?php

namespace App\Repository;

use App\Entity\Sponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sponsor>
 *
 * @method Sponsor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sponsor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sponsor[]    findAll()
 * @method Sponsor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsor::class);
    }

    public function save(Sponsor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sponsor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sponsor[] Returns an array of Sponsor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sponsor
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

 public function findSponsorByNom($nom)
    {
        return $this->createQueryBuilder('sponsorSponsor')
            ->where('sponsorSponsor.nom LIKE  :nom')
            ->setParameter('nom', '%'.$nom. '%')
            ->getQuery()
            ->getResult();
    }
    public function findNewSponsor()
{
    
    return $this->createQueryBuilder('c')
        ->select('count(c.nomSponsor)')
        ->andWhere('c.invest = :val')
        ->setParameter('val', 'biat')
        ->getQuery()->getSingleScalarResult()

    ;
}

public function findSponsorsTritée()
{
    $date = new \DateTime();
    return $this->createQueryBuilder('c')
        ->select('count(c.invest)')
        ->andWhere('c.invest != :val')
       ->andWhere('c.nomSponsor = :val1')
        ->setParameter('val', 'Financier' )
        ->setParameter('val1', 'biat')
        ->getQuery()->getSingleScalarResult()
        ;
}

public function findSponsorsNonTritée()
{
    
    return $this->createQueryBuilder('c')
        ->select('count(c.invest)')
        ->andWhere('c.invest = :val')
        ->andWhere('c.nomSponsor = :val1')
        ->setParameter('val', 'Financier' )
        ->setParameter('val1','biat')
        ->getQuery()->getSingleScalarResult()
        ;
}
}
