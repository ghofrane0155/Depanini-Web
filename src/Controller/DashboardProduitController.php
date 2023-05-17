<?php

namespace App\Controller;

use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardProduitController extends AbstractController
{
    /** Back **/
    #[Route('/dashboardproduit', name: 'dashboard_produit')]
    public function list_produits(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Produits::class);
        $list = $repo->findAll();

        return $this->render('Back/dashboard_produit/index.html.twig', [
            'dashboard_produit' => $list,
            'num_products' => count($list),
        ]);
    }


    #[Route('/DeleteProduit/{id}', name: 'DeleteProduit')]
    public function DeleteProduit(ManagerRegistry $doctrine, $id): Response
    {
        $repo = $doctrine->getRepository(Produits::class);
        $em = $doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();

        return $this->redirectToRoute('dashboard_produit');
    }


    /* #[Route('/listProduit', name: 'listP')]
    public function listProduit(ManagerRegistry $doctrine): Response
    {
        $repoProd=$doctrine->getRepository(Produits::class);
        $produits=$repoProd->findAll();

        return $this->render('produit/list.html.twig', [
            'produits' => $produits,
        ]);
    } */
}
