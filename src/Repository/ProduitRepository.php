<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produits>
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    public function save(Produits $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

        /**
        * @return Product[] Returns an array of Product objects
        */
        public function findWithSearch($search)
        {
        $query = $this->createQueryBuilder('p');
        //dd($query->getQuery()); visualise la requete.
        if ($search->getMinPrice()){
        $query = $query->andwhere('p.prixProduit > '.$search->getMinPrice());
        } //structuration de la requete avec les paramètres
        if ($search->getMaxPrice()){
        $query = $query->andwhere('p.prixProduit< '.$search->getMaxPrice());
        }
        if($search->getCategories()){
            $query = $query->join('p.category', 'c')
                           ->andwhere('c.id IN (:categories)")
                           ->setParameter(categories',$search->getCategories());
        }
        //dd($query);
        //dd($query->getQuery()->getResult());
        return $query->getQuery()->getResult();
        ;
        }

    // Additional repository methods can be added here
}
