<?php

namespace App\Controller;

use DateTimeImmutable;

use App\Entity\Produits;
use App\Entity\Commandes;
use App\Form\AddProduitType;
use App\Form\UpdateProduitType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class CRUDProduitController extends AbstractController
{
    #[Route('/crudProduit', name: 'app_crudProduit')]
    public function index(): Response
    {
        return $this->render('crud_produit/index.html.twig', [
            'controller_name' => 'CRUDProduitController',
        ]);
    }



    #[Route('/addProduit', name: 'app_addproduit', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine, Request $request, ProduitRepository $produitRepository,  UserRepository $userRepository): Response
    {

        $commandesRepo = $doctrine->getRepository(Commandes::class);
        $commandes = $commandesRepo->findAll();
        $produit = new Produits();
        $form = $this->createForm(AddProduitType::class, $produit);
        $form->handleRequest($request);

        if (!empty($produit->getBarecode())) {
            // barecode property is not empty, do nothing
        } else {
            // barecode property is empty, generate a random number
            $produit->setBarecode(mt_rand(100000, 999999));
        }
        // Generate the barcode image and save it to a file




        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $idclient = $user->getUserIdentifierID();
            $produit->setIdUser($idclient);

            $image = $form['image']->getData();
            if ($image) {
                $imageString = $image->getClientOriginalName();

                $produit->setImageProduit("Front/images/" . $imageString);
            }
            $generator = new BarcodeGeneratorPNG();
            /* $barcodeData = $generator->getBarcode($produit->getBarecode(), $generator::TYPE_CODE_128);
                    $barcodeImage = imagecreatefromstring($barcodeData);

                    $barcodeImage = '<img src="data:image/png;base64,' . base64_encode($barcodeData) . '">';*/
            $nom_produit = str_replace(' ', '', $produit->getNomProduit());
            $file_path = "Front/images/barcodes/{$nom_produit}_barcode.jpg";
            $barcode =  $produit->getNomProduit();
            file_put_contents($file_path, $generator->getBarcode($barcode, $generator::TYPE_CODE_128));

            //  fonction eli ta3tini date mta3 lyoum 
            $current_time = new DateTimeImmutable();
            //badalaet el date mta3 produit 9bal save
            $produit->setDateCreation($current_time);


            $produitRepository->save($produit, true);
            $this->addFlash('success', 'Product added succefuly!');

            return $this->redirectToRoute('show_produit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/add_produit/front.html.twig', [
            'produit' => $produit,
            'addproduit' => $form,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $produit->getIdUser()])
            ),

        ]);
    }
    #[Route('/showProduit', name: 'show_produit')]
    public function list_produits(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Produits::class);
        $list = $repo->findAll();
        $commandesRepo = $doctrine->getRepository(Commandes::class);
        $commandes = $commandesRepo->findAll();

        $user = $this->getUser();
            $idclient = $user->getUserIdentifierID();


        return $this->render('Front/crud_produit/showProduit.html.twig', [
            'show_produit' => $list,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $idclient])
            ),
        ]);
    }
    #[Route('/mesProduits', name: 'mes_produits')]
    public function mes_produits(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Produits::class);
        $list = $repo->findAll();

        $commandesRepo = $doctrine->getRepository(Commandes::class);
        $commandes = $commandesRepo->findAll();
        $user = $this->getUser();
            $idclient = $user->getUserIdentifierID();


        return $this->render('Front/crud_produit/mesProduits.html.twig', [
            'mes_produits' => $list,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $idclient])
            ),
        ]);
    }
    #[Route('/DeleteMyProduct/{id}', name: 'DeleteMyProduct')]
    public function DeleteProduit(ManagerRegistry $doctrine, $id): Response
    {
        $repo = $doctrine->getRepository(Produits::class);
        $em = $doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();

        $this->addFlash('success', 'Product deleted succefuly 😊');


        return $this->redirectToRoute('mes_produits');
    }
    #[Route('/updateProduit/{id}', name: 'update_produit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(UpdateProduitType::class, $produit);
        $form->handleRequest($request);

            $user = $this->getUser();
                $idclient = $user->getUserIdentifierID();

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();
            if ($image) {
                $imageString = $image->getClientOriginalName();

                $produit->setImageProduit("Front/images/" . $imageString);
            }

            $produitRepository->save($produit, true);
            $this->addFlash('success', 'Product updated succefuly 😊');



            return $this->redirectToRoute('mes_produits', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/crud_produit/updateProduit.html.twig', [
            'produit' => $produit,
            'updateproduit' => $form,
            'num_products' => count(
                $this->getDoctrine()
                     ->getRepository(Commandes::class)
                     ->findBy(['idUser' => $idclient])
            ),
        ]);
    }
    #[Route('/searchProduit', name: 'search_produit', methods: ['GET', 'POST'])]
    public function searchproduct(Request $request, ProduitRepository $produitRepository): Response
    {
        $produits = $this->$produitRepository->findAll();
        $search = new Produits();
        $form = $this->createForm(SearchProductType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produits = $this->$produitRepository->findWithSearch($search);
        }
        return $this->renderForm('Front/crud_produit/showProduit.html.twig', [
            'produits' => $produits,
            'searchproduit' => $form,
        ]);
    }
    #[Route('/filtre_produit/{category}', name: 'filtre_produit')]
    public function filtre(Request $request, ProduitRepository $produitRepository, $category): Response
    {
        $form = $this->createFormBuilder()
            ->add('categorieProduit', ChoiceType::class, [
                'choices' => [
                    '---- Please select ----' => '',
                    'Design' => 'Design',
                    'Education' => 'Education',
                    'Formation' => 'Formation',
                    'Conception' => 'Conception',
                    'Mobile' => 'Mobile',
                    'Montage' => 'Montage',
                    'Publicite' => 'Publicite',
                    'Gaming' => 'Gaming',
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control show-tick'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        $criteria = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['categorieProduit']) {
                $criteria['categorie'] = $data['categorieProduit'];
            }
        }

        $produits = $produitRepository->findBy($criteria);
        $categories = $produitRepository->getAllCategories();

        return $this->render('Front/produitFront/search.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }
}
