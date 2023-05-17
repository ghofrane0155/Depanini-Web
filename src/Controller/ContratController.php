<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Recrutements;

use App\Form\ContratType;

use App\Repository\ContratRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class ContratController extends AbstractController
{
 /**
 * @IsGranted("ROLE_ADMIN")
 */   
    #[Route('/contrat', name: 'afficher_contrat')]
    public function index(ContratRepository $repository): Response
    {

        $Contrat = $repository->findAll(); //definition du var Contrat

        return $this->render('Back/contrat/index.html.twig', [
            'contrat' => $Contrat
        ]);
    }
 
    #[Route('/pdf/{id}', name: 'contrat_pdf', methods: ['GET'])]
    public function pdf(Contrat $contrat): Response
{
    // create new PDF document
    $dompdf = new Dompdf();
    
    // generate HTML content for the document
    $html = $this->renderView('Front/ContratFront/pdf.html.twig', [
        'contrat' => $contrat, 
        
    ]);

    // load HTML into the PDF document
    $dompdf->loadHtml($html);

    // set paper size and orientation
    

    // render PDF document
    $dompdf->render();

    // create a response object to return the PDF file
    $response = new Response($dompdf->output());
    
    // set content type to application/pdf
    $response->headers->set('Content-Type', 'application/pdf');

    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_INLINE,
        'contrat.pdf'
    );
    $response->headers->set('Content-Disposition', $disposition);

    return $response;
}




    #[Route('/frontcontrat', name: 'afficher_frontcontrat')]
    public function frontcontrat(ContratRepository $repository): Response
    {

        $Contrat = $repository->findAll();

        return $this->render('Front/ContratFront/frontaffiche.html.twig', [
            'contrat' => $Contrat
        ]);
    }


    #[Route('/Deletecontrat/{id}', name: 'supp_contrat')]
    function Supprimer($id, ContratRepository $rep, ManagerRegistry $doctrine)
    {
        $Contrat = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Contrat);
        $em->flush();
        return $this->redirectToRoute('afficher_frontcontrat');
    }
    #[Route('/supprimer_contrat/{id}', name: 'supprimer_contrat')]
    function Supprimerfront($id, ContratRepository $rep, ManagerRegistry $doctrine)
    {
        $Contrat = $rep->find($id);
        $em = $doctrine->getManager();
        $em->remove($Contrat);
        $em->flush();
        return $this->redirectToRoute('afficher_contrat');
    }



    #[Route('/ajout_contrat', name: 'ajout_contrat')]
    function Ajout(Request $request,SluggerInterface $slugger)
    {
        $Contrat=new Contrat();
        $form=$this->createForm(ContratType::class,$Contrat);

        $form->add('Ajouter' , SubmitType::class) ;
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
           

           
            
            
            $em=$this->getDoctrine()->getManager();
            
            $em->persist( $Contrat);
            $em->flush();   
            
            return $this->redirectToRoute('afficher_frontcontrat');

        }
        return $this->render('Front/ContratFront/index.html.twig',[
            'form'=>$form->createView(),

        ]);
    }

    #[Route('/modifier_contrat/{id}', name: 'modifier_contrat')]
    function update(ContratRepository $repo,$id,Request $request , ManagerRegistry $doctrine,SluggerInterface $slugger){
        $Contrat = $repo->find($id) ;
        $form=$this->createForm(ContratType::class,$Contrat) ;
        $form->add('update' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()){

          
           
            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('afficher_frontcontrat');

        }
        return $this->render('Front/ContratFront/updatecontrat.html.twig' , [
            'form' => $form->createView()
        ]) ;

    }
    



    }

