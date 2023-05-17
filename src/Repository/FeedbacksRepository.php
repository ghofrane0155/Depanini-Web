<?php

namespace App\Repository;

use App\Entity\Feedbacks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedbacks>
 *
 * @method Feedbacks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedbacks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedbacks[]    findAll()
 * @method Feedbacks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbacksRepository extends ServiceEntityRepository
{






    //


    //

    //
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedbacks::class);
    }

    public function save(Feedbacks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Feedbacks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Feedbacks[] Returns an array of Feedbacks objects
     */


    public function getTotalStarsByUser(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT u.idUser, u.nomUser, u.prenomUser, u.photoUser, SUM(f.stars) AS totalStars 
                FROM App\Entity\Feedbacks f 
                JOIN f.idClient u 
                GROUP BY u.idUser'
        );
        return $query->getResult();
    }

    public function getTotalStarsByUserID($id): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(f.stars) AS totalStars 
                FROM App\Entity\Feedbacks f 
                WHERE f.idClient = :id'
        )
        ->setParameter('id', $id);
        
        return (int) $query->getSingleScalarResult();
    }



    public function findFeedbacksByClient(int $clientId): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('u.nomUser', 'u.prenomUser','u.photoUser' ,'f.idFeedback','f.commentaire', 'f.stars')
           ->join('f.idFreelancer', 'u')
           ->where('f.idClient = :clientId')
           ->setParameter('clientId', $clientId);
           
        return $qb->getQuery()->getResult();
    }

    public function findFeedbacksByFreelancerapi(int $clientId): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('u.nomUser', 'u.prenomUser','u.photoUser' ,'f.idFeedback','f.commentaire', 'f.stars')
           ->join('f.idFreelancer', 'u')
           ->where('f.idFreelancer = :clientId')
           ->setParameter('clientId', $clientId);
           
        return $qb->getQuery()->getResult();
    }

    public function countallStar(): int
    {
        return $this->createQueryBuilder('f')
            ->select('SUM(f.stars) as totalStars')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFeedbackCountForFreelancerAndClient($freelancerId, $clientId)
    {
        $result =0;
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(f.idFeedback) as nb_feedbacks
    FROM App\Entity\Feedbacks f
    WHERE f.idFreelancer = :id_freelancer
    AND f.idClient = :id_client'
        )->setParameter('id_freelancer', $freelancerId)
            ->setParameter('id_client', $clientId);

        $result = $query->getSingleScalarResult();
        return $result;
    }


    public function getFeedbackStats() {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f.stars, COUNT(f.idFeedback) as count')
           ->groupBy('f.stars');
    
        $query = $qb->getQuery();
        $results = $query->getResult();
    
        $total = $this->createQueryBuilder('f')
                      ->select('COUNT(f.idFeedback)')
                      ->getQuery()
                      ->getSingleScalarResult();
    
        foreach ($results as &$result) {
            $result['percentage'] = ($result['count'] / $total) * 100;
        }
    
        return $results;
    }
    //    public function findOneBySomeField($value): ?Feedbacks
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
