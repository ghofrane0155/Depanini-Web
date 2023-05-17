<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Offres;
use App\Entity\Users;

use App\Form\OffreType;
use App\Repository\CategoriesRepository;
use App\Repository\OffreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Component\HttpClient\HttpClient;

use Doctrine\Persistence\ManagerRegistry;





use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
class OffreController extends AbstractController
{
    /////////
    ////*/
    ///

    ////
    ///
    /////
    #[Route('/offre', name: 'afficher_offre')]
    public function index(OffreRepository $repository): Response
    {

        $Offre = $repository->findAll();

        return $this->render('Back/offre/index.html.twig', [
            'offre' => $Offre
        ]);
    }
    #[Route('/frontoffre', name: 'afficher_frontoffre')]
    public function frontoffre(Request $request,OffreRepository $repository ,PaginatorInterface $paginator): Response
    {

        $Offre = $repository->findAll();
        $Offre = $paginator->paginate(
            $Offre, /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('Front/offreFront/frontaffiche.html.twig', [
            'offre' => $Offre
        ]);
    }


    #[Route('/Deleteoffre/{id}', name: 'supp_offre')]
    function Supprimer($id, OffreRepository $rep, ManagerRegistry $doctrine)
    {
        $Offre = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Offre);
        $em->flush();
        return $this->redirectToRoute('afficher_offre');
    }
    #[Route('/supprimer_offre/{id}', name: 'supprimer_offre')]
    function Supprimerfront($id, OffreRepository $rep, ManagerRegistry $doctrine)
    {
        $Offre = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Offre);
        $em->flush();
        return $this->redirectToRoute('afficher_frontoffre');
    }



    #[Route('/ajout_offre', name: 'ajout_offre')]
    function Ajout(Request $request,SluggerInterface $slugger)
    {
        $Offre=new Offres();
        $form=$this->createForm(OffreType::class,$Offre);

        $form->add('Ajouter' , SubmitType::class) ;
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            // $brochureFile = $form->get('imageOffre')->getData();

            // // this condition is needed because the 'brochure' field is not required
            // // so the PDF file must be processed only when a file is uploaded
            // if ($brochureFile) {
            //     $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
            //     // this is needed to safely include the file name as part of the URL
            //     $safeFilename = $slugger->slug($originalFilename);
            //     $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

            //     // Move the file to the directory where brochures are stored
            //     try {
            //         $brochureFile->move(
            //             $this->getParameter('imageOffre'),
            //             $newFilename
            //         );
            //     } catch (FileException $e) {
            //         // ... handle exception if something happens during file upload
            //     }

            //     // updates the 'brochureFilename' property to store the PDF file name
            //     // instead of its contents
            //     $Offre->setimageOffre($newFilename);
            // }

            $img = $form['image']->getData();  
        if($img){
            $imageString = $img->getClientOriginalName();
            $Offre->setimageOffre("Front/images/".$imageString);
        }
            $description = $Offre->getDescriptionOffre();
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', 'https://neutrinoapi.net/bad-word-filter', [
                'query' => [
                    'content' => $description
                ],
                'headers' => [
                    'User-ID' => '11112022', 
                    'API-Key' => '1n6sNR9jhwhSggpK02eHmI4HmeWTgap5wzHhMCnvA8NLt4nk',
                ]
            ]);
            

            
            if ($response->getStatusCode() === 200) 
            {
                $result = $response->toArray();
                if ($result['is-bad']) {
                    // Handle bad word found
                    $this->addFlash('danger', '</i>Your comment contains inappropriate language and cannot be posted.');
                    return $this->redirectToRoute('bad_word');
                } else {
                    $em=$this->getDoctrine()->getManager();
                    $user = $em->getRepository(Users::class)->find(1);
                    $Offre->setIdUser($user) ;
                    $em->persist($Offre);
                    $em->flush();   
                    
                    return $this->redirectToRoute('afficher_frontoffre');
                }
            } 
            
            else{
                
                return new Response("Error processing request", Response::HTTP_INTERNAL_SERVER_ERROR);
            }  
            }
         
         
        return $this->render('Front/offreFront/index.html.twig',[
            'form'=>$form->createView(),

        ]);
    }

    #[Route('/modifier_offre/{id}', name: 'modifier_offre')]
    function update(OffreRepository $repo,$id,Request $request , ManagerRegistry $doctrine,SluggerInterface $slugger){
        $Offre = $repo->find($id) ;
        $form=$this->createForm(OffreType::class,$Offre) ;
        $form->add('update' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()){

            $brochureFile = $form->get('imageOffre')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('imageOffre'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $Offre->setimageOffre($newFilename);
                $ems = $this->getDoctrine()->getManager();
                $user = $ems->getRepository(Users::class)->find(1);
                $Offre->setIdUser($user) ;
            }
            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('afficher_frontoffre');

        }
        return $this->render('Front/offreFront/updateoffre.html.twig' , [
            'form' => $form->createView()
        ]) ;

    }

    #[Route('/bad_word', name: 'bad_word')]

    function Affiche_bad(OffreRepository $repository){
        $Offre= $repository->findAll();
        return $this->render('Front/offreFront/bad_words.html.twig',['description'=>$Offre]);
    }

    #[Route('/offre_get', name: 'afficher_offre_rest')]
    public function indexOffre_list_api(OffreRepository $repository): Response
    {

        $Offre = $repository->findAll();
        $data= []; 
        foreach ($Offre as $off) {
        $data [] =[
            'idOffre'=> $off ->getIdOffre(),
            'prixOffre'=> $off ->getPrixOffre(),
            'descriptionOffre'=> $off ->getDescriptionOffre(),
            'localisationOffre'=> $off ->getLocalisationOffre(),
            'nomOffre'=> $off ->getNomOffre(),
            'imageOffre'=> $off ->getImageOffre(),
            'typeOffre'=> $off ->getTypeOffre(),
            'idUser'=> $off ->getIdUser()->getIdUser(),
        

        ];
        }
        return $this->json ($data,200,['Content-Type'=> 'application/json' ]);
            
        
    }

    #[Route('/add_offreApi', name: 'ajout_offreAPI')]   
    function Ajout_offreApi(Request $req, EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        
        $idUser = $req->get('idUser');
        $userRepository = $doctrine->getRepository(Users::class)->find($idUser);
        $idCategorie = $req->get('idCategorie');
        $categoriesRepository=$doctrine->getRepository(Categories::class)->find($idCategorie);


        
        $Offre=new Offres();
       

        $Offre->setPrixOffre($req->get('prix'));
        $Offre->setDescriptionOffre($req->get('description'));
        $Offre->setLocalisationOffre($req->get('localisation'));
        $Offre->setNomOffre($req->get('nom'));
       // $Offre->setImageOffre($req->get('image'));
        $Offre->setTypeOffre($req->get('type'));
        $Offre->setIdUser( $userRepository);
        $Offre->setCategorie($categoriesRepository);

 
        $entityManager->persist($Offre);
        $entityManager->flush();
    
    
        return $this->json([
            'success' => 'Reclamation a été créé avec succès!',
        ]);
    }
    #[Route('/Deleteoffre_api/{id}', name: 'supp_offre_api')]
    function Deleteoffre_api($id, OffreRepository $rep, ManagerRegistry $doctrine)
    {
        $Offre = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Offre);
        $em->flush();
        return new JsonResponse("delete with success");
    }
}