<?php

namespace App\Controller;


use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class PaymentController extends AbstractController

{




    #[Route('/pay', name: 'pay')]

    public function index(): Response
    {

        return $this->render('Front/crud_produit/pay.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
    #[Route('/checkout/{id}', name: 'checkout_page')]

    public function checkout($stripeSK, ManagerRegistry $doctrine, $id): Response
    {
        \Stripe\Stripe::setApiKey($stripeSK);
        $produit = $doctrine
            ->getRepository(Produits::class)
            ->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $stripe = new \Stripe\StripeClient('sk_test_51N1asdG5b4qjOB6CeqxbEt1KKnqCDAzRGcWkfE22y7Czv9daSsFNxgSPMuA8Qst3RAPo2IuR5pzXH2NlCRNxkvSp00W9h3Sewq');

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $produit->getNomProduit(),
                    ],
                    
                    'unit_amount' => $produit->getPrixProduit() * 100,
                ],
                'quantity' => 1,
                
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        

        /**dd($session); dd($produit); */
        return $this->redirect($session->url, 303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response
    {

        return $this->render('Front/crud_produit/success.html.twig', []);
    }
    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {

        return $this->render('Front/crud_produit/cancel.html.twig', []);
    }
}
