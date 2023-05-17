<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Events>
 *
 * @method Events|null find($id, $lockMode = null, $lockVersion = null)
 * @method Events|null findOneBy(array $criteria, array $orderBy = null)
 * @method Events[]    findAll()
 * @method Events[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }

    public function save(Events $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Events $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Events[] Returns an array of Events objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneBySomeId($id): ?Events
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.idevent = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function lastThree()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.datedebutevent', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult() ;
    }

    public function displayLastThreeEvents(int $id, EntityManagerInterface $entityManager)
    {
        $events = $entityManager->createQueryBuilder()
            ->select('e')
            ->from(Event::class, 'e')
            ->where('e.id < :id')
            ->setParameter('id', $id)
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    } 
/////////////////////////////////////////////////////////////////

    public function findEventByName($name){
        $entityManager=$this->getEntityManager();
        $query=$entityManager
            ->createQuery("SELECT event FROM APP\Entity\Events 
            event WHERE event.nomevent = :name")
            ->setParameter('nomevent',$name);
            
        return $query->getResult();
    }
}
