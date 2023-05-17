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
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class SecurityController extends AbstractController
{
    /**************************************************** */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }
    /**************************************************** */
    #[Route(path: '/signup', name: 'app_signup')]
    public function signup(mail $mail, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher, VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form2 = $this->createForm(SignUpType::class, $user);
        $form2->handleRequest($request);
        $user->setRoles(["ROLE_FREELANCER"]);
        
        if ($form2->isSubmitted() && $form2->isValid()) {
            $user->setPhotoUser("Front/images/user1.png");

           
            if (strcmp($form2->get('role')->getData(), "ROLE_CLIENT") === 0)
                $user->setRoles(["ROLE_CLIENT"]);

            // $user->setPassword(hash('sha256', $form2->get('password')->getData()));
            // /************************************** */

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form2->get('password')->getData()
                )
            );

             /************************************** */
            // $user->setPassword($form2->get('password')->getData());

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getIdUser(),
                $user->getEmail(),
                ['id' => $user->getIdUser()]
            );

            // //BUNDLE MAILER////////////////////////////////
            $to = $user->getEmail();
            $templatePath = 'security/verify.html.twig';
            $templateData = [
                'user' => $user->getLogin(),
                'verifyUrl' => $signatureComponents->getSignedUrl(),
            ];
            //dd($signatureComponents);
            $mail->sendEmail($to, 'Verify your acccount', $templatePath, $templateData);
            $this->addFlash('success', 'E-mail  de verification du compte a été envoyer avec succes ');
            ////////////////////////////////////////////////////

            return $this->redirectToRoute('app_index');
        }
        return $this->render('security/signUp.html.twig', [
            'form2' => $form2->createView(),
        ]);
    }
    /*********************************************************** */
    #[Route(path: '/verify', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getIdUser(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_signup');
        }
        $user->setIsVerified(true);
        $entityManager->flush();
        $this->addFlash('success', 'Account Verified! You can now log in.');
        return $this->redirectToRoute('app_login');
    }
    /******************************************************* */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('logout() should never be reached');
    }
    /***********send a link to Reset password*************************************** */
    #[Route(path: '/FindAccount', name: 'FindAccount')]
    public function FindAccount(mail $mail, Request $request, UserRepository $userRepository, TokenGeneratorInterface  $tokenGenerator)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();

            $user = $userRepository->findOneBy(['email' => $donnees]);
            if (!$user) {
                $this->addFlash('danger', 'cette adresse n\'existe pas');
                return $this->redirectToRoute("FindAccount");
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreur est survenue :' . $exception->getMessage());
                return $this->redirectToRoute("app_login");
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // //BUNDLE MAILER////////////////////////////////
            $to = $user->getEmail();
            $templatePath = 'security/reset_passMail.html.twig';
            $templateData = [
                'user' => $user->getLogin(),
                'resetUrl' => $url,
            ];
            $mail->sendEmail($to, 'Reset Password', $templatePath, $templateData);
            $this->addFlash('message', 'E-mail  de réinitialisation du Mot de passe a été envoyer avec succes ');

            return $this->redirectToRoute("app_login");
        }
        return $this->render("security/FindAccount.html.twig", ['form' => $form->createView()]);
    }
    /********Reset password after clicking the link********************************************** */
    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')]
    public function resetpassword(Request $request, string $token)
    {
        $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['reset_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'TOKEN INCONNU');
            return $this->redirectToRoute("app_login");
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($request->request->get('password'));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message', 'Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");
        } else {
            return $this->render("security/ChangePass.html.twig", ['token' => $token]);
        }
    }
    // /*******to fix later (send sms code)***************************************** */
    #[Route(path: '/Verification', name: 'Verification')]
    public function Verification(UserRepository $userRepository, Request $request)
    {
        //$code= $userRepository->generateRandomCode(6);
        $code = "123";
        $form = $this->createFormBuilder()
            ->add('code', TextType::class)
            ->add('Next', SubmitType::class, ['label' => $code])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $c = $data['code'];

            if (strcmp($c, $code) == 0) {
                //dd($this->token);
                // $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);
                // return new RedirectResponse($url);
                return $this->render('security/ChangePass.html.twig');
            }
        }
        return $this->render('security/VerifCode.html.twig', ['form' => $form->createView(), 'code' => $code]);
    }
    /******************************************** */

    /**
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    #[Route('/search', name: 'search')]
    public function search(Request $request, UserRepository $u): Response
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

        return $this->render('Front/user/searchProfil.html.twig', [
            'User' => $users,
        ]);
    }
}
