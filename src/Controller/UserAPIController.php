<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserType;
use App\Repository\FeedbacksRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class UserAPIController extends BaseController
{
/*************editProfil************************************************* */
#[Route('/profilAPI', name: 'ProfilAPI')]
public function editProfilAPI(Request $req,NormalizerInterface $normalizer)
{       
    
    $id=$req->get("id");
    $nomUser=$req->query->get('nom');
    $PrenomUser=$req->query->get('prenom');
    $Login=$req->query->get('login');

    //$datenais = new \DateTime($req->query->get('date'));
    $Password=$req->query->get('password');

    $Email=$req->query->get('email');
    $Adresse=$req->query->get('adresse');
    $Tel=$req->query->get('tel');
    // $Sexe=$req->query->get('sexe');

    $em=$this->getDoctrine()->getManager();
    $user=$em->getRepository(Users::class)->find($id);
    if($req->files->get("photo")!=null){
        $file=$req->files->get("photo");
        $fileName=$file->getClientOriginalName();

        $file->move($fileName);
        $user->setPhotoUser($fileName);

    }

    $user->setNomUser($nomUser);
    $user->setPrenomUser($PrenomUser);
    $user->setLogin($Login);
//////////////////////////////////////////////////////
    $user->setPassword ($Password);

   // $user->setDateNaisUser($datenais);
    $user->setEmail($Email);
    $user->setAdresse($Adresse);
    $user->setTel($Tel);
    // $user->setSexe($Sexe);

    try{  
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse("success",200);//http result ta3 server ok
    }catch (\Exception $ex){
        return new Response("fail ".$ex->getMessage());
    }
  //  $jsonContent=$normalizer->normalize($user,'json',['groups'=>'Events']);
  //  return $this->json($jsonContent,200,['Content-Type'=>'application/json']);
}
/***************************************************************** */
#[Route('/api/me',name:'app_user_api_me')]
public function apiMe(): Response
{       
    return $this->json($this->getUser(),200,[],[
        'groups' =>['user:read']
    ]);
}
/*********************************************** */
#[Route('/getPasswordByEmail',name:'app_password')]
public function getPasswordByEmail(Request $request)
{       
    $email=$request->get('email');
    $user=$this->getDoctrine()->getManager()->getRepository(Users::class)->findOneBy(['email'=>$email]);
    if($user){
        $password=$user->getPassword();
        $seralizer=new Serializer([new ObjectNormalizer()]);
        $formatted=$seralizer->normalize($password);
        return new JsonResponse($formatted);
    }
    return new Response("user not found");
}
/*************************************** */
    #[Route('/updatepassword',name:'up_password')]

    public function updatepassword(Request $request, NormalizerInterface $normalizer ,  SerializerInterface  $serializer , UserRepository $utilisateurRepository,UserPasswordEncoderInterface $encoder)
    {

        $user = $utilisateurRepository->find($request->get("id"));
        $password = $request->get('password', '');
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $utilisateurRepository->save($user,true);

        $serializedUser = $normalizer->normalize($user, 'json', ['groups' => 'user']);

        return new JsonResponse($serializedUser);
    }
}
