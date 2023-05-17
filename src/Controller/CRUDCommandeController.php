<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\Commandes;
use App\Entity\Users;
use DateTimeImmutable;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class CRUDCommandeController extends AbstractController
{
    #[Route('/crud/commande', name: 'crud_commande')]
    public function index(): Response
    {
        return $this->render('crud_commande/index.html.twig', [
            'controller_name' => 'CRUDCommandeController',
        ]);
    }
    
    #[Route('/DeleteMyitem/{id}', name: 'DeleteMyitem')]
    public function Deleteitem(ManagerRegistry $doctrine, $id): Response
    {
        $repo = $doctrine->getRepository(Commandes::class);
        $em = $doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();

        return $this->redirectToRoute('mes_commandes');
    }

   

    #[Route('/mesCommandes', name: 'mes_commandes')]
    public function mes_commandes(ManagerRegistry $doctrine): Response
    {
        $commandesRepo = $doctrine->getRepository(Commandes::class);
        $commandes = $commandesRepo->findAll();
    
        $produitsRepo = $doctrine->getRepository(Produits::class);
        $user = $this->getUser();
            $idclient = $user->getUserIdentifierID();
    
        $products = [];
    
        foreach ($commandes as $commande) {
            $produit = $produitsRepo->find($commande->getIdProduit());
            if ($produit !== null) {
                $products[$commande->getIdProduit()] = $produit;
            }
        }
        /* dd($products); */
        return $this->render('Front/crud_produit/mesCommandes.html.twig', [
            'mes_commandes' => $commandes,
            'products' => $products,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $idclient])
            ),
        ]);
    }
    




    #[Route('/addtocart/{idprod}', name: 'add_to_cart')]
    public function addToCart($idprod, ManagerRegistry $doctrine, CommandeRepository $commandeRepository)
    {
        // nekhou beha l current user
        /** $user = $this->getUser();*/
        $user = $this->getUser();
        $idclient = $user->getUserIdentifierID();

        /*$id = $idprod;*/
        // Get the product entity
        $product = $doctrine
            ->getRepository(Produits::class)
            ->find($idprod);



        // Create a new cart item entity
        $commande = new Commandes();
        $commande->setIdUser($idclient);
        $commande->setIdProduit($product->getIdProduit());
        // hethi el fonction eli ta3tini date mta3 lyoum izi
        $current_time = new DateTimeImmutable();
        //hethi bech badalaet el date mta3 produit 9bal ma savi <3 
        $commande->setDateCommande($current_time);

        // Persist the new cart item entity
        $commandeRepository->saveitem($commande, true);

        // Redirect to the cart page
        return $this->redirectToRoute('mes_commandes', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/showCart', name: 'show_cart')]
    public function showcart(ManagerRegistry $doctrine): Response
    {
        $commandesRepo = $doctrine->getRepository(Commandes::class);
        $commandes = $commandesRepo->findAll();

        $user = $this->getUser();
            $idclient = $user->getUserIdentifierID();
    
        $produitsRepo = $doctrine->getRepository(Produits::class);
    
        $products = [];
        $totalPrice = 0;
    
        foreach ($commandes as $commande) {
            $produit = $produitsRepo->find($commande->getIdProduit());
            if ($produit !== null) {
                $products[$commande->getIdProduit()] = $produit;
                $totalPrice += $produit->getPrixProduit();
            }
        }
        /* dd($products); 
        dd($totalPrice);*/
        return $this->render('Front/crud_produit/showCart.html.twig', [
            'mes_commandes' => $commandes,
            'products' => $products,
            'total_price' => $totalPrice,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $idclient])
            ),
            
            
        ]);
    }
}
