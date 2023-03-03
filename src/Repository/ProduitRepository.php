<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findProduitByNom($Nom)
    {
         return $this->createQueryBuilder('Produit')
         ->where('Produit.nom LIKE : nom')
         ->setParameter('nom' , '%'.$Nom.'%')
         ->getQuery()
         ->getResult();
    }

   /* public function findAllWithRating()
    {
        $qb = $this->createQueryBuilder('p')
                  ->leftJoin('p.ratings', 'r')
                  ->addSelect('AVG(r.rating) as ratingAverage')
                  ->groupBy('p.id');

        return $qb->getQuery()->getResult();
    }*/
    /**
     * Returns all Annonces per page
     * @return void 
     */
    public function getPaginatedAnnonces($page, $limit){
        $query = $this->createQueryBuilder('a')
        
        ->setFirstResult(($page * $limit) - $limit)
        ->setMaxResults($limit)
    ;
    return $query->getQuery()->getResult();
}


public function getTotalProduits($filters = null){
    $query = $this->createQueryBuilder('a')
        ->select('COUNT(a)')
       
    
   ;

    return $query->getQuery()->getSingleScalarResult();
}
public function findByPriceRange($minPrice, $maxPrice)
{
    return $this->createQueryBuilder('p')
        ->where('p.prix >= :minPrice')
        ->andWhere('p.prix <= :maxPrice')
        ->setParameter('minPrice', $minPrice)
        ->setParameter('maxPrice', $maxPrice)
        ->getQuery()
        ->getResult();
}








/*public function searchprix($Prix_Produit)
    {
        $EM=$this->getEntityManager();
        $query = $EM->createQuery('select v from App\Entity\Produit v  WHERE v.prix  BETWEEN :a AND :b ')
            ->setParameter('a', 0)
            ->setParameter('b', $Prix_Produit);
        return $query->getResult();


    }*/







//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
