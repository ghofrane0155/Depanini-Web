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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class DashboardUserAPIController extends AbstractController
{
    function getUserGenderStatsAPI(EntityManagerInterface $entityManager): array
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
    #[Route('/dashboardUserAPI', name: 'dashboard_userAPI')]
    public function ListUserAPI(ManagerRegistry $doctrine,NormalizerInterface $Normalizer)
    {
        $user=$this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        // Get the gender statistics for the users
        $Stats = $this->getUserGenderStatsAPI($entityManager);

        $repo=$doctrine->getRepository(Users::class);
        $list=$repo->findAll();
       
       
        // Render the template and pass in the gender statistics as a variable
        $jsonContent=$Normalizer->normalize($list,'json',['groups' =>['user:read']]);
        return new Response(json_encode($jsonContent));
    }
/***************delete************************************ */ 
// #[Route('/DeleteUserAPI/{id}', name: 'DeleteUserAPI')]
// public function DeleteUserAPI(ManagerRegistry $doctrine,$id,NormalizerInterface $Normalizer)
// {
//     $repo=$doctrine->getRepository(Users::class);
//     $em=$doctrine->getManager();
//     $user=$repo->find($id);
//     $em->remove($user);
//     $em->flush();

//     $jsonContent=$Normalizer->normalize($user,'json',['groups' =>['user:read']]);
//     return new Response("user deleted successfully ".json_encode($jsonContent));
// }
#[Route('/DeleteUserAPI', name: 'DeleteUserAPI')]
public function DeleteUserAPI(Request $request, SerializerInterface  $serializer  , UserRepository $utilisateurRepository)
{
    $user = $utilisateurRepository->find($request->get("id"));
    if($user)
    {
        $utilisateurRepository->remove($user,true);
        $formatted = $serializer->normalize("User Deleted ");
        return new JsonResponse($formatted);
    }
    else
    {
        return new Response(json_encode(null));
    }
}
/*************************************************** */

    #[Route('/users/{id}', name: 'get_user_by_id')]
    public function getUserById(int $id): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->find(13);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create an array or object with the user's data
        $userData = [
            'id' => $user->getIdUser(),
            'name' => $user->getNomUser(),
            'email' => $user->getEmail(),
            // ...
        ];

        // Serialize the user data to JSON and return it in the response
        $jsonUserData = json_encode($userData);
        $response = new Response($jsonUserData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
