<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\SignUpType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Form\ForgotPasswordType;
use App\Service\mail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Twilio\Rest\Client;

class SecurityAPIController extends AbstractController
{
    /**************************************************** */
    #[Route(path: '/loginAPI', name: 'app_loginAPI')]
    public function loginAPI(Request $request,NormalizerInterface $normalizer,UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher)
    {
        // $user=$userRepository->findOneBy([
        //     'email' => $request->get('email')
        // ]);            
        // $jsonContent = $normalizer->normalize([],'json',['groups'=>'user:read']);
        // return new Response(json_encode($jsonContent));
        $Email = $request->query->get("email");
        $Password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findOneBy(['email'=>$Email]);

        if($user) {
            //hashed password
            if(password_verify($Password,$user->getPassword())){
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
/***********without hashage********************** */
            // if($Password == $user->getPassword()) {
            //     $serializer = new Serializer([new ObjectNormalizer()]);
            //     $formatted = $serializer->normalize($user);
            //     return new JsonResponse($formatted);
            // }
            else {
                return new Response("password not found");
            }
        }
        else 
        {
            return new Response("failed");
        }
    }
    /**************************************************** */
    #[Route(path: '/signupAPI', name: 'app_signupAPI')]
    public function signupAPI(Request $req,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Users();
        $datenais = new \DateTime($req->get('dateNaisUser'));

        $user->setNomUser($req->get('nomUser'));
        $user->setPrenomUser($req->get('prenomUser'));
        $user->setLogin($req->get('login'));
         $user->setPassword(
             $userPasswordHasher->hashPassword(
                $user,
                $req->get('password')
            )
        );
     //   $user->setPassword($req->get('password'));

        $user->setDateNaisUser($datenais);
        $user->setEmail($req->get('email'));
        $user->setAdresse($req->get('adresse'));
        $user->setTel($req->get('tel'));
        $user->setSexe($req->get('sexe'));
        $user->setRole($req->get('role'));
        if (strcmp($req->get('role'), "ROLE_CLIENT") === 0)
              $user->setRoles(["ROLE_CLIENT"]);
        else
              $user->setRoles(["ROLE_FREELANCER"]);
        $user->setPhotoUser("Front/images/user1.png");
        
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($user);

    $sid    = "AC996f5d67ff7718f75549161540ba1e32";
$token  = "57ae0c797a8253df22be61086378c3ba";
    // Send SMS notification using Twilio
    $sid = 'AC996f5d67ff7718f75549161540ba1e32';
    $token = '57ae0c797a8253df22be61086378c3ba';
    $twilioNumber = '+13159037947'; // Enter your Twilio phone number here

    $client = new Client($sid, $token);
    $message = $client->messages->create(
        '+21653414061', // Enter the recipient's phone number here
        [
            'from' => $twilioNumber,
            'body' => 'Bienvenue à Depanini! votre inscription a ete faite aved succes'
        ]
    );
    return new JsonResponse($formatted);
    }
    /******************************************** */
    /**
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    #[Route('/searchAPI', name: 'searchAPI')]
    public function searchAPI(Request $request, UserRepository $u,NormalizerInterface $normalizer): Response
    {
        $query = $request->query->get('query');

        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.nomUser LIKE :query')
            ->orWhere('u.prenomUser LIKE :query')
            ->orWhere('u.login LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
        //dd($users);


        //$stars=$u->getTotalStarsByUserID($users->getIdUser());

        $jsonContent=$normalizer->normalize($users,'json',['groups' =>['user:read']]);
    return new Response(json_encode($jsonContent));
    }
/******************************************************* */
// #[Route('/rechercheAPI', name: 'rechercheAPI')]
// public function getUserByEmail(string $email,NormalizerInterface $normalizer)
// {
//     $user= $this->createQueryBuilder('u')
//         ->andWhere('u.email = :email')
//         ->setParameter('email', $email)
//         ->getQuery()
//         ->getOneOrNullResult()
//     ;
//     $jsonContent=$normalizer->normalize($user,'json',['groups' =>['user:read']]);
//     return new Response(json_encode($jsonContent));
// }
}
