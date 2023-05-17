<?php

namespace App\Repository;

use App\Entity\Reclamations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamations>
 *
 * @method Reclamations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamations[]    findAll()
 * @method Reclamations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{

    //

    //

    //

    //

    //
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamations::class);
    }

    public function save(Reclamations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reclamations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Reclamations[] Returns an array of Reclamations objects
     */
    public function reclamationsAvecUsers(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.idUser','u')
            ->addSelect('u')           
            ->getQuery()
            ->getResult()
        ;
    }


    public function countOpen(): int
{
    return $this->createQueryBuilder('r')
        ->select('COUNT(r.idReclamation)')
        ->where('r.statut = :statut')
        ->setParameter('statut', 'Resolu')
        ->getQuery()
        ->getSingleScalarResult()
    ;
}

public function countrec(): int
{
    return $this->createQueryBuilder('r')
        ->select('COUNT(r)')
        ->getQuery()
        ->getSingleScalarResult();
}

    public function oneReclamationsAvecUsers($id): array
    {
        return $this->createQueryBuilder('r')->where('r.idReclamation = :id')
            ->join('r.idUser','u')
            ->addSelect('u')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult()
            ;
    }

//    public function findOneBySomeField($value): ?Reclamations
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
