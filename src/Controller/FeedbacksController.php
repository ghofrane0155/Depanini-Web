<?php

namespace App\Controller;

use App\Entity\Feedbacks;
use App\Entity\Users;
use App\Form\FeedbacksType;
use App\Repository\FeedbacksRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
#[Route('/feedback')]
class FeedbacksController extends AbstractController
{

  /**
     * @Route("/comments/new", name="new_comment")
     */
    #[Route('/new/{id_simpleuser}', name: 'new_feedback')]
    public function new(int $id_simpleuser, Request $request, FeedbacksRepository $repo, UserRepository $userrepo, FlashyNotifier $flashy): Response
    {
        $feedback = new Feedbacks();
        $form = $this->createForm(FeedbacksType::class, $feedback);
        $form->handleRequest($request);
        $criteria = ['idUser' => $id_simpleuser];
        $simpleuser = $userrepo->findBy($criteria);
            $simpleuser=$simpleuser[0];
        if ($form->isSubmitted() && $form->isValid()) {
            // condition 
            // 7achti bel id mta3 user connecter $
            $user = $this->getUser();
            $idFreelancer = $user->getUserIdentifierID();
            $idClient = $id_simpleuser;
            
            //dd($idClient,$idFreelancer);
            $feedbacks_user_limit = $repo->getFeedbackCountForFreelancerAndClient($idFreelancer, $idClient);
            if ($feedbacks_user_limit >= 3) {
                $flashy->error("Vous n'avez pas le droit de soumettre un feedback plusieurs fois au même utilisateur.");
                return $this->render('Front/visiteurpages/feedback.html.twig', [
                    'form' => $form->createView(),
                    'simpleuser' => $simpleuser
                ]);
            }
            $enti = $this->getDoctrine()->getManager();
            $userfreelancerArray = $enti->getRepository(Users::class)->findBy(['idUser' => $idFreelancer]);
            $userClientArray = $enti->getRepository(Users::class)->findBy(['idUser' => $idClient]);

            //  dd($idFreelancer,$idClient,$feedbacks_user_limit);
            // la date d'aujourd'hui
            $datefeedback = date('Y-m-d');
            $feedback->setDate(DateTime::createFromFormat('Y-m-d', $datefeedback));

            $userfreelancer = $userfreelancerArray[0]; 
            $feedback->setIdFreelancer($userfreelancer);    
            $userClient = $userClientArray[0]; 
            $feedback->setIdClient($userClient);

            $userfreelancer = $userfreelancerArray[0]; 
            $feedback->setIdFreelancer($userfreelancer);    
            $userClient = $userClientArray[0]; 
            $feedback->setIdClient($userClient);

            $flashy->success('Votre feedback a ete envoyer avec success!', 'Votre feedback a ete envoyer avec success');


            // save the comment to the database or do other processing
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feedback);
            $entityManager->flush();


            return $this->render('Front/visiteurpages/feedback.html.twig', [
                'form' => $form->createView(),
                'simpleuser' => $simpleuser
            ]);
        }
        $flashy->warning('Vous devez remplir le formulaire.', '');

        return $this->render('Front/visiteurpages/feedback.html.twig', [
            'form' => $form->createView(),
            'simpleuser' => $simpleuser
        ]);
    }



    #[Route('deletef/{idFeedback}', name: 'feedbacks_delete')]
    public function delete(int $idFeedback, FeedbacksRepository $feedbacksRepository, ManagerRegistry $doctrine): Response
    {
        $feedback = $feedbacksRepository->find($idFeedback);


        $em = $doctrine->getManager();
        $em->remove($feedback);
        $em->flush();
        return $this->redirectToRoute('dashboard_home');
    }
    #[Route('/feedbackpage', name: 'feedbackp')]
    public function feedbackpage(Request $request): Response
    {
        return $this->render('Front/visiteurpages/feedbacknewpages.html.twig', []);
    }

    #[Route('/updatef_api/{id}', name: 'updatef')]
    public function updateFeedback(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $feedback = $entityManager->getRepository(Feedbacks::class)->find($id);       
        $stars = floatval($request->query->get('stars'));
        $feedback->setStars($stars);
        // Set the new values for the feedback entity
        $feedback->setCommentaire($request->query->get('commentaire'));
      
        
        // Save the updated feedback entity to the database
        $entityManager->flush();
    
        // Return a success response
        return $this->json(['message' => 'Feedback updated successfully']);
    }

    #[Route('/deletefeedback_api/{idFeedback}', name: 'deletefeedback_api')]
    public function deletefeedback_api(int $idFeedback, FeedbacksRepository $feedbacksRepository, ManagerRegistry $doctrine)
    {
        $feedback = $feedbacksRepository->find($idFeedback);


        $em = $doctrine->getManager();        
        $em->remove($feedback);
        $em->flush();
        return new JsonResponse("delete with success");
    }


    #[Route('/feedback_total_stars_api', name: 'feedbackp_api_nbstars')]
    public function feedback_total_stars_api(FeedbacksRepository $f): Response
    {
        // user curent userid
        $allStars = $f->getTotalStarsByUserID(1);
        return $this->json($allStars, 200, ['Content-Type' => 'application/json']);


    }

    #[Route('/user_details_api', name: 'user_details')]
    public function user_details_api( FeedbacksRepository $feedrepo): Response
    {        
      
        $allfeedbacks_of_user = $feedrepo->findFeedbacksByFreelancerapi(1);

        return $this->json($allfeedbacks_of_user, 200, ['Content-Type' => 'application/json']);
    }
}
