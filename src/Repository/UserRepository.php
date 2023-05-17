<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
// use Twilio\Rest\Client; 


/**
 * @extends ServiceEntityRepository<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

// require_once __DIR__ . '/../vendor/autoload.php';

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function save(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
/**************************************************************** */
    public function findUserByEmail($recherche)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email LIKE :val')
            ->setParameter("val", $recherche . '%')
            ->getQuery()
            ->getResult();
    }
   
/**************************************************************** */

public function findUserById($idUser): array
    {
        return $this->createQueryBuilder('u')->where('u.idUser = :idU')
            ->setParameter('idU',$idUser)
            ->getQuery()
            ->getResult()
            ;
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


/******************************youssef: get id user from email temporary ********************************** */

public function findUseridByEmail($email): array
{
    return $this->createQueryBuilder('u')->select('u.id')->where('u.email = :emaillll')
        ->setParameter('emaillll',$email)
        ->getQuery()
        ->getResult()
        ;
}



/**************************************************************** */


//    /**
//     * @return Users[] Returns an array of Users objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }




// public function generateRandomCode(int $length): string
// {
//     $min = pow(10, $length - 1);
//     $max = pow(10, $length) - 1;
//     $code = random_int($min, $max);
//     return strval($code);
// }


public function findToken(): Users
    {
        return $this->createQueryBuilder('u')
            ->where('u.reset_token IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
/********************************************** */
// public function sendsms(): void
//     {
//         //require ('vendor\autoload.php');
//         $sid = "AC934a20606febcdbee3b2e843e162c750" ; 
//         $token = "f6839ba7273e36839702468ddeaed1dc" ; 
//         $client = new Client ($sid, $token);

//         $message = $client->messages
//             ->create("+21653414061", // to
//                 ["body" => "vous avez un nouveau offre sur BePro!", "from" => "+447897022710"]
//             );

//     }

/****************************************************************/
}
