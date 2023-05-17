<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Entity\Users;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\ReclamationType;
use App\Repository\AdminRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
class ReclamationController extends AbstractController
{
    // /**
    //  * @IsGranted("ROLE_ADMIN")
    //  */
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(ReclamationRepository $repo, Request $request, SessionInterface $session): Response
    {
        $recList = $repo->reclamationsAvecUsers();
        return $this->render('Back/reclamation/index.html.twig', [
            'listReclamations' => $recList,
        ]);
    }
    //API rest pour afficher la liste des reclamations 
    #[Route('/reclamation_get', name: 'app_reclamation_rest')]
    public function Reclamation_list_rest_api(ReclamationRepository $repo)
    {
        $recList = $repo->reclamationsAvecUsers();
        // puisque j'ai une cle etrangere 
        $data = [];
        foreach ($recList as $rec) {
            $data[] = [
                'idReclamation' => $rec->getIdReclamation(),
                'type' => $rec->getType(),
                'description' => $rec->getDescription(),
                'dateReclamation' => $rec->getDateReclamation(),
                'pieceJointe' => $rec->getPieceJointe(),
                'statut' => $rec->getStatut(),
                'dateResolution' => $rec->getDateResolution(),
                'idAdmin' => $rec->getIdAdmin()->getIdAdmin(),
                'idUser' => $rec->getIdUser()->getIdUser(),
            ];
        }
        return $this->json($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/reclamationpdf', name: 'pdfreclamation')]
    public function printpdf(ReclamationRepository $repo): Response
    {
        //définir les options
        $pdfOptions = new Options();

        //police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->setChroot('');

        //instancier Dompdf
        $pdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $pdf->setHttpContext($context);

        //générer le html
        $img = file_get_contents('Back/logooo.jpg');
        $imgData = base64_encode($img);
        $imgSrc = 'data:image/jpeg;base64,' . $imgData;

        $recList = $repo->reclamationsAvecUsers();

        $html = $this->renderView('Back/reclamation/pdf.html.twig', [
            'listReclamations' => $recList,
            'img' => $imgSrc
        ]);

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();


        $pdfData = $pdf->output();

        // Return the PDF as a Response object
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Depanini-reclamation.pdf"',
        ];

        return new Response($pdfData, 200, $headers);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/deleteReclamation/{id}', name: 'deleteReclamation')]
    public function deleteReclamationById($id, ManagerRegistry $doctrine)
    {
        $getrec = $doctrine->getRepository(Reclamations::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($getrec);
        $em->flush();

        $this->addFlash('removerec', 'Votre Reclamation a été supprimer avec succès!');


        return $this->redirectToRoute('app_reclamation');
    }

    #[Route('/deleteReclamation_api/{id}', name: 'deleteReclamation_api')]
    public function deleteReclamation_api($id, ManagerRegistry $doctrine)
    {
        $getrec = $doctrine->getRepository(Reclamations::class)->find($id);
        $em = $doctrine->getManager();
        if ($getrec !== null) {
            $em->remove($getrec);
            $em->flush();
            return new JsonResponse("delete with success");
        }


        return new JsonResponse("id not found");
    }



    #[Route('/addReclamation', name: 'addReclamation')]
    public function addReclamation(AdminRepository $adminRepository, UserRepository $userRepository, ManagerRegistry $doctrine, Request $request)
    {
        //recuperation de l'admin
        $admin = $adminRepository->find(1);

        $reclamation = new Reclamations();
        //creation formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation)
            ->add('ajouter', SubmitType::class);
        $form->remove('idAdmin');
        $form->remove('dateResolution');
        $form->remove('dateReclamation');
        $form->remove('idUser');

        $form->remove('statut');
        $form->handleRequest($request);
    
        $user = $this->getUser();
       
    
        // dd($request->get('dateReclamation'));

        if ($form->isSubmitted() && $form->isValid()) {
            // la date d'aujourd'hui
            $dateReclamation = date('Y-m-d');
            $reclamation->setDateReclamation(DateTime::createFromFormat('Y-m-d', $dateReclamation));
            $reclamation->setIdAdmin($admin);
            if ($user != null){                  
                $reclamation->setIdUser($user);
            }else{                    
                $reclamation->setIdUser(null);
            }
            // recuperer le nom de Pices jointe
            $file = $form['pieceJointe']->getData();
            $filename = $file->getClientOriginalName();
            $reclamation->setPieceJointe($filename);

            // handle file uploading
            $pdfDirectory = $this->getParameter('pdf_directory');
            $file->move($pdfDirectory, $filename);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre Reclamation a ete envoyer avec success!');

            return $this->render('Front/visiteurpages/addReclamation.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('Front/visiteurpages/addReclamation.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/addReclamation_api', name: 'addReclamation_api')]
    public function addReclamation_api(AdminRepository $adminRepository, ReclamationRepository $recrepo, Request $request, UserRepository $us, ManagerRegistry $doctrine)
    {
        //dd($request->query->get("type"));
        
        //recuperation de l'admin
        $admin = $adminRepository->find(1);

    
        

        // Extract the required fields from the payload
        $type = $request->query->get("type");
        $description = $request->query->get("description");
        //$dateReclamation = $data['dateReclamation'] ?? null;
        $pieceJointe = "";
        //  $statut = $data['statut'] ?? null;
        //   $dateResolution = $data['dateResolution'] ?? null;


        
        // Retrieve the user based on the provided $idClient

        $reclamation = new Reclamations();


        $reclamation->setType($type);
        $reclamation->setDescription($description);
        $reclamation->setPieceJointe($pieceJointe);
        $reclamation->setStatut("Ouvert");



        // la date d'aujourd'hui
        $dateReclamation = date('Y-m-d');
        $reclamation->setDateReclamation(DateTime::createFromFormat('Y-m-d', $dateReclamation));
        $reclamation->setIdAdmin($admin);
        //curent user

        $user = $us->findBy(['idUser' => 13]);

        $reclamation->setIdUser($user[0]);


        $recrepo->save($reclamation, true);
        return $this->json([
            'success' => 'Reclamation a été créé avec succès!',
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/detailsReclamation/{id}', name: 'detailsReclamation')]
    public function detailsReclamationById($id, ReclamationRepository $repo): response
    {

        $rec = $repo->oneReclamationsAvecUsers($id);
        return $this->render('Back/reclamation/details.html.twig', [
            'OneReclamations' => $rec,
        ]);
    }



    #[Route('/detailsReclamation_api/{id}', name: 'detailsReclamation_api')]
    public function detailsReclamationById_api(Reclamations $rec): response
    {
        //     $rec = $repo->oneReclamationsAvecUsers($id);

        $data = [];

        //     $data[] = [
        //         'idReclamation' => $rec[0]->getIdReclamation(),
        //         'type' => $rec[0]->getType(),
        //         'description' => $rec[0]->getDescription(),
        //         'dateReclamation' => $rec[0]->getDateReclamation(),
        //         'pieceJointe' => $rec[0]->getPieceJointe(),
        //         'statut' => $rec[0]->getStatut(),
        //         'dateResolution' => $rec[0]->getDateResolution(),
        //         'idAdmin' => $rec[0]->getIdAdmin()->getIdAdmin(),
        //         'idUser' => $rec[0]->getIdUser()->getIdUser(),
        // ];

        $data[] = [
            'idReclamation' => $rec->getIdReclamation(),
            'type' => $rec->getType(),
            'description' => $rec->getDescription(),
            'dateReclamation' => $rec->getDateReclamation(),
            'pieceJointe' => $rec->getPieceJointe(),
            'statut' => $rec->getStatut(),
            'dateResolution' => $rec->getDateResolution(),
            'idAdmin' => $rec->getIdAdmin()->getIdAdmin(),
            'idUser' => $rec->getIdUser()->getIdUser(),
        ];

        return $this->json($data, 200, ['Content-Type' => 'application/json']);
    }

    // count all reclamation API
    #[Route('/nbrre_api', name: 'nbrre_api')]
    public function nbrRec(ReclamationRepository $repo): response
    {
        $nbrre = $repo->countrec();
        return $this->json($nbrre, 200, ['Content-Type' => 'application/json']);
    }
    // count all reclamation API
    #[Route('/nbrre_resolu_api', name: 'nbrre_res_api')]
    public function nbrre_resolu_api(ReclamationRepository $repo): response
    {
        $nbrre = $repo->countOpen();
        return $this->json($nbrre, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/updateReclamation/{id}', name: 'updateReclamation')]
    public function updateReclamationById(SessionInterface $session, UserRepository $userRepository, AdminRepository $adminRepository, ManagerRegistry $doctrine, $id, Request $request, EntityManagerInterface $em, ReclamationRepository $repo): Response
    {
        $rec = $repo->oneReclamationsAvecUsers($id);
        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationType::class, $reclamation)
            ->add('update', SubmitType::class);
        $form->remove('idAdmin');
        $form->remove('idUser');
        $form->remove('dateResolution');
        $form->remove('dateReclamation');
        $form->remove('type');
        $form->remove('description');
        $form->remove('pieceJointe');

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //convert to date
            $dateString = $request->get('dateReclamationnn');
            $date = DateTime::createFromFormat("l j F Y - H:i", $dateString);
            $date_formatted = $date;

            $admin = $adminRepository->find(1);
            $reclamation->setType($rec[0]->getType());
            $reclamation->setDescription($rec[0]->getDescription());
            $reclamation->setDateReclamation($rec[0]->getDateReclamation());
            $reclamation->setPieceJointe($rec[0]->getPieceJointe());

            $user = $userRepository->findUserByEmail($rec[0]->getIdUser()->getEmail());
            $reclamation->setDateResolution($date_formatted);
            $reclamation->setIdReclamation($id);
            $reclamation->setIdAdmin($admin);
            $reclamation->setIdUser($user[0]);

            //dd($reclamation);
            $entity = $em->merge($reclamation);
            $em->remove($entity);
            $em->flush();

            $em = $doctrine->getManager();
            $em->persist($reclamation);
            $em->flush();

            $this->addFlash('updatesuccess', 'Votre Reclamation a été mise à jour avec succès!');

            return $this->redirectToRoute('app_reclamation');
        }

        return $this->render('Back/reclamation/update.html.twig', [
            'OneReclamations' => $rec,
            'updatefrom' => $form->createView(),
            'formErrors' => $form->getErrors(true, false)
        ]);
    }

    // #[Route('/updateReclamation_api/{id}', name: 'updateReclamation_api')]
    // public function updateReclamationById_api($id,Request $request,ManagerRegistry $doctrine){
    //     $em = $doctrine->getManager();
    //     $getrec = $doctrine->getRepository(Reclamations::class)->find($id);
    //     $getrec->setObject($request->get("object"));

    //     $em->persist($getrec);
    //     $em->flush();

    //     return new JsonResponse("updated");
    // }



}
