<?php

namespace App\Controller;

use App\Entity\Recrutements;
use App\Entity\Users;

use App\Form\RecrutementsType;

use App\Repository\RecrutementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;

use Doctrine\Persistence\ManagerRegistry;

use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class RecrutementsController extends AbstractController
{
    
    #[Route('/recrutement', name: 'afficher_recrutement')]
    public function index(RecrutementsRepository $repository): Response
    {

        $Recrutements = $repository->findAll();

        return $this->render('Back/recrutements/index.html.twig', [
            'recrutements' => $Recrutements
        ]);
    }
    #[Route('/frontrecrutement', name: 'afficher_frontrecrutement')]
    public function frontrecrutement(RecrutementsRepository $repository): Response
    {

        $Recrutements = $repository->findAll();

        return $this->render('Front/RecrutementsFront/frontaffiche.html.twig', [
            'recrutement' => $Recrutements 
        ]);
    }
    // #[Route('/search', name: 'contrat_search')]
    // public function search(Request $request, EntityManagerInterface $em)
    // {
    //     $form = $this->createForm(SearchType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $query = $form->get('q')->getData();

    //         $Recrutements = $em->getRepository(Recrutements::class)->createQueryBuilder('p')
    //             ->where('p.description LIKE :query')
    //             ->setParameter('query', '%' . $query . '%')
    //             ->getQuery()
    //             ->getResult();

    //         return $this->render('Front/RecrutementsFront/search_results.html.twig', [
    //             'recrutement' => $Recrutements
    //         ]);
    //     }
    // }


    #[Route('/Deleterecrutement/{id}', name: 'supp_recrutement')]
    function Supprimer($id, RecrutementsRepository $rep, ManagerRegistry $doctrine)
    {
        $Recrutements = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Recrutements);
        $em->flush();
        return $this->redirectToRoute('afficher_recrutement');
    }
    #[Route('/supprimer_recrutement/{id}', name: 'supprimer_recrutement')]
    function Supprimerfront($id, RecrutementsRepository $rep, ManagerRegistry $doctrine)
    {
        $Recrutements = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Recrutements);
        $em->flush();
        return $this->redirectToRoute('afficher_frontrecrutement');
    }



    #[Route('/ajout_recrutement', name: 'ajout_recrutement')]
    function Ajout(Request $request,SluggerInterface $slugger,ManagerRegistry $doctrine)
    {
        $Recrutements=new Recrutements();
        $form=$this->createForm(RecrutementsType::class,$Recrutements);
        $form->add('Ajouter' , SubmitType::class) ;
        $form->handleRequest($request);
        
        $user = $this->getUser();    

        if($form->isSubmitted()){
            dump($form->getErrors(true));
            $Recrutements->setIdclient($user);

            $em=$doctrine->getManager();
            $em->persist($Recrutements);
            $em->flush();

            
            return $this->redirectToRoute('afficher_frontrecrutement');

        }
        return $this->render('Front/RecrutementsFront/index.html.twig',[
            'form'=>$form->createView(),

        ]);
    }

    #[Route('/modifier_recrutement/{id}', name: 'modifier_recrutement')]
    function update(RecrutementsRepository $repo,$id,Request $request , ManagerRegistry $doctrine,SluggerInterface $slugger){
        $Recrutements = $repo->find($id) ;
        $form=$this->createForm(RecrutementsType::class,$Recrutements) ;
        $form->add('update' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()){

          
           
            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('afficher_frontrecrutement');

        }
        return $this->render('Front/recrutementsFront/updaterecrutement.html.twig' , [
            'form' => $form->createView()
        ]) ;

    }



    }

