<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardUserController extends AbstractController
{
    function getUserGenderStats(EntityManagerInterface $entityManager): array
{
    // Get the total number in the database
    $userCount = $entityManager->getRepository(Users::class)->count([])-1;
    $maleCount = $entityManager->getRepository(Users::class)->count(['sexe' => 'Homme'])-1;
    $femaleCount = $entityManager->getRepository(Users::class)->count(['sexe' => 'Femme']);
    $freelancerCount = $entityManager->getRepository(Users::class)->count(['roles' => 'ROLE_FREELANCER']);
    $clientCount = $entityManager->getRepository(Users::class)->count(['roles' => 'ROLE_CLIENT']);

    // Calculate the percentage 
    $malePercent = $userCount > 0 ? round(($maleCount / $userCount) * 100, 2) : 0;
    $femalePercent = $userCount > 0 ? round(($femaleCount / $userCount) * 100, 2) : 0;
    $freelancerPercent = $userCount > 0 ? round(($freelancerCount / $userCount) * 100, 2) : 0;
    $clientPercent = $userCount > 0 ? round(($clientCount / $userCount) * 100, 2) : 0;

    // Return the statistics as an array
    return [
        'total_users' => $userCount,
        'male_users' => $maleCount,
        'male_percent' => $malePercent,
        'female_users' => $femaleCount,
        'female_percent' => $femalePercent,
        'freelancer_users' => $freelancerCount,
        'freelancer_percent' => $freelancerPercent,
        'client_users' => $clientCount,
        'client_percent' => $clientPercent,
    ];
}
/***************affiche************************************ */ 
    #[Route('/dashboardUser', name: 'dashboard_user')]
    public function ListUser(ManagerRegistry $doctrine): Response
    {
        $user=$this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        // Get the gender statistics for the users
        $Stats = $this->getUserGenderStats($entityManager);

        $repo=$doctrine->getRepository(Users::class);
        $list=$repo->findAll();
       
       
        // Render the template and pass in the gender statistics as a variable
        return $this->render('Back/dashboard_user/DetailsUsers.html.twig', [
            'dashboard_user' => $list,'Stats' => $Stats,'u'=>$user,
        ]);
    }
/***************delete************************************ */ 
#[Route('/DeleteUser/{id}', name: 'DeleteUser')]
public function DeleteUser(ManagerRegistry $doctrine,$id): Response
{
    $repo=$doctrine->getRepository(Users::class);
    $em=$doctrine->getManager();
    $em->remove($repo->find($id));
    $em->flush();

    return $this->redirectToRoute('dashboard_user');
}
/*************************************************** */
// #[Route('/recherche/by-email')]
// public function rechercheParSociete(Request $request, UserRepository $userRepository): Response
// {
//     $searchValue = $request->get('search-value');
//     if ($searchValue != null) {
//         $users = $userRepository->findUserByEmail($searchValue);

//         if ($users) {
//             return new JsonResponse($users);
//         } else {
//             return new JsonResponse(null);
//         }
//     } else {
//         return new JsonResponse(null);
//     }
// }
}
