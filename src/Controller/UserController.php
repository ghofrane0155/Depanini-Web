<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserType;
use App\Repository\FeedbacksRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserController extends BaseController
{
/*************editProfil************************************************* */
#[Route('/profil', name: 'Profil')]
public function editProfil(ManagerRegistry $doctrine,Request $request,FeedbacksRepository $feedrepo): Response
{       
    $user=$this->getUser();
    $form=$this->createForm(UserType::class,$user);
    $form->handleRequest($request);

    $number_start_user = $feedrepo->getTotalStarsByUserID($user->getIdUser());

    $repo=$doctrine->getRepository(Users::class);
    $u=$repo->find($user->getIdUser());

    if($form->isSubmitted() && $form->isValid()){
        dump($form->getErrors(true));

        $img = $form['image']->getData();  
        if($img){
            $imageString = $img->getClientOriginalName();
            //$user->setPhotoUser("Front/images/".$imageString);
            $user->setPhotoUser($imageString);
        }
        
        $em=$doctrine->getManager();
        $em->persist($u);
        $em->flush();
         return $this->redirectToRoute('Profil');
    }
    return $this->render('Front/user/Profil.html.twig', [
        'u'=>$user,'form' => $form->createView(),'number_start_user' => $number_start_user,
    ]);
}
/***************************************************************** */
}
