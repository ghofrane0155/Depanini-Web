<?php

namespace App\Controller;

use App\Entity\Admins;
use App\Entity\Feedbacks;
use App\Repository\FeedbacksRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardHomeController extends AbstractController
{
    /***************affiche************************************ */
    #[Route('/dashboardhome', name: 'dashboard_home')]
    public function dashboard_home(ManagerRegistry $doctrine, FeedbacksRepository $f): Response
    {
        $repo = $doctrine->getRepository(Admins::class);
        $list = $repo->findAll();


        $Feedbacks = $f->getTotalStarsByUser();
        $allStars = $f->countallStar();
        $FeedbacksStat=$f->getFeedbackStats();
        //dd($FeedbacksStat);

        return $this->render('Back/dashboard_home/index.html.twig', [
            'dashboard_home' => $list,
            'Feedbacks' => $Feedbacks,
            'allStars' => $allStars,
            'feedbacksstat' => $FeedbacksStat,


        ]);
    }

    #[Route('user_details/{iduser}', name: 'user_details')]
    public function delete(int $iduser, UserRepository $userRepository, FeedbacksRepository $feedrepo): Response
    {
        $user = $userRepository->find($iduser);
        $number_start_user = $feedrepo->getTotalStarsByUserID($user->getIdUser());
        $allfeedbacks_of_user = $feedrepo->findFeedbacksByClient($user->getIdUser());

        dd($user->getIdUser(), $number_start_user, $allfeedbacks_of_user);
        return $this->render('/feedbacks/userdetailsfeedback.html.twig', [
            'user' => $user,
            'number_start_user' => $number_start_user,
            'allfeedbacks_of_user' => $allfeedbacks_of_user,
        ]);
    }
}
