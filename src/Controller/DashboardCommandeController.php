<?php

namespace App\Controller;

use App\Entity\Commandes;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class DashboardCommandeController extends AbstractController
{
    /** Back **/
/**
 * @IsGranted("ROLE_ADMIN")
 */
    #[Route('/dashboardcommande', name: 'dashboard_commande')]
    public function list_commandes(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Commandes::class);
        $list = $repo->findAll();

        return $this->render('Back/dashboard_commande/index.html.twig', [
            'dashboard_commande' => $list,
            'num_commandes' => count($list),
        ]);
    }

/**
 * @IsGranted("ROLE_ADMIN")
 */
    #[Route('/Deleteitem/{id}', name: 'Deleteitem')]
    public function Deleteitem(ManagerRegistry $doctrine, $id): Response
    {
        $repo = $doctrine->getRepository(Commandes::class);
        $em = $doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();

        return $this->redirectToRoute('dashboard_commande');
    }


    /* #[Route('/listCommande', name: 'listP')]
    public function listCommande(ManagerRegistry $doctrine): Response
    {
        $repoProd=$doctrine->getRepository(Commandes::class);
        $commandes=$repoProd->findAll();

        return $this->render('commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    } */
}
