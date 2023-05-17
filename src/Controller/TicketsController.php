<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\Events;
use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TicketsRepository;
use App\Form\TicketsType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
class TicketsController extends AbstractController
{
    
    /**************fetch Tickets on the admin dashboard**************/
    #[Route('/tickets', name: 'tickets', methods: ['GET'])]
    public function index2(TicketsRepository $ticketsRepository): Response
    {
       
        $tickets = $this->getDoctrine()
        ->getRepository(Tickets::class)
        ->findAll();

        return $this->render('Back/Tickets/affichageBack.html.twig', [
            'tickets' => $tickets,
        ]);
    }
   
    
    #[Route('/addTicket/{id}', name: 'addTicket',methods: ['GET', 'POST'])]
    public function fetchToTicket(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,Pdf $knpSnappyPdf, Events $event, UserRepository $userRepository,Request $request,EntityManagerInterface $em,$id): Response
    {
         // Find the event entity by its ID
    $event = $this->getDoctrine()->getRepository(Events::class)->find($id);   
    $eventDate = $event->getDatedebutevent(); 
    $remainingTicketsCount = $this->getRemainingTicketsCount($event);
        // If there are no remaining tickets, return an error message
        if ($remainingTicketsCount <= 0) {
            $this->addFlash('error', 'Sorry, there are no more tickets available for this event.');
            return $this->redirectToRoute('ReadAllEvents');
        }
        $now = new \DateTime();
        $diff = date_diff($now, $eventDate);
        $remainingDays = $diff->format('%r%a');
        if ($remainingDays > 0) {
            $remainingDaysString = $remainingDays . ' day' . ($remainingDays > 1 ? 's' : '') . ' remaining';
        } elseif ($remainingDays == 0) {
            $remainingDaysString = 'Today';
        } else {
            $remainingDaysString = abs($remainingDays) . ' day' . (abs($remainingDays) > 1 ? 's' : '') . ' ago';
        }
    
    // create a new Tickets instance and populate it with data from the Events entity
    $ticket = new Tickets();
    $ticket->setIdEvent($event); 

    // Create a new instance of the tickets form and pass in the event entity
    $form = $this->createForm(TicketsType::class, $ticket);
    $form->handleRequest($request);
    
    $user = $this->getUser();
    
    if ($form->isSubmitted() ) {
        $user = $this->getUser();

        $idUser=$user->getUserIdentifierID();
        $login=$user->getUserIdentifierLOGIN();

        $ticket->setIdevent($event);
        // id
        $enti = $this->getDoctrine()->getManager();
        $userArray = $enti->getRepository(Users::class)->findBy(['idUser' => $idUser]);
        $userArray2 = $enti->getRepository(Users::class)->findBy(['login' => $login]);
        $id=$userArray[0];
        $ticket->setIdUser($id);
        // login
        $login=$userArray2[0];
        $ticket->setLogin($login);

        $event->getPrixevent();
        $ticket->setPrixtotale($ticket->getQuantite() * $event->getPrixevent());

        // Save the ticket entity to the database
        $ticket = $form->getData();
        $entityManager = $doctrine->getManager();
        $entityManager->persist($ticket);
        $entityManager->flush();
        
        $data =  'your Ticket Id :'.$ticket->getIdticket().'     ' .
        'Event name :'.$ticket->getIdevent().'       '.
        'your Email :'.$ticket->getIdUser().'         '.
        'your Login :'.$ticket->getLogin().'       '.
        'Quantity :'.$ticket->getQuantite().'       '.
        'Total Price :'.$ticket->getPrixtotale().'       '.
        'reminder : you can not enter the event if you do not show as this QR code !!';
             
        
        $QR=$this->generateQrCode($data,$ticket->getIdticket());

        $this->addFlash('success', 'thank you for participating . your ticket is added to the bascket , stay TUNED!!');
        return $this->redirectToRoute('ticketsUser');
        
    }
    
    return $this->render('Front/Tickets/CreateTickets.html.twig', [
         'form' => $form->createView(),
         'event' => $event,
         'ticket'=>$ticket,
         'user' => $this->getUser(),
         'remainingDaysString' => $remainingDaysString,
         'remainingTicketsCount' => $remainingTicketsCount,
         
    ]); 

    }

    /*******************************************************/

    #[Route('/addTicketapi', name: 'addTicketapi')]
public function fetchToTicketapi(Request $req, EntityManagerInterface $entityManager): Response
{
    // Find the event entity by its ID
    $event = $entityManager->getRepository(Events::class)->find($req->get('idevent'));

    
    // create a new Tickets instance and populate it with data from the Events entity
    $ticket = new Tickets();
    $ticket->setIdEvent($event); // set the event object instead of the ID
    $userRepository = $this->getDoctrine()->getRepository(Users::class);
$user = $userRepository->find($req->get('idUser'));
$ticket->setIdUser($user);

    $ticket->setLogin($req->get('login'));
    $ticket->setQuantite($req->get('quantite'));
    $ticket->setPrixtotale($req->get('prixtotale'));

    $entityManager->persist($ticket);
    $entityManager->flush();

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($ticket);

    return new JsonResponse($formatted);
}

    
    

    /*************************QRCode************************/
    public function generateQrCode(string $data,int $id)
    {
        
        
        $qr_code = QrCode::create($data)
                 ->setSize(100)
                 ->setMargin(10)
                 ->setForegroundColor(new Color(0, 0, 0))
                 ->setBackgroundColor(new Color(255, 255, 255))
                 ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh);

        $label = Label::create(mt_rand(100000, 999999))
                 ->setTextColor(new Color(1, 0, 0))
                 ->setAlignment(new LabelAlignmentLeft);
        $writer = new PngWriter;
        $result = $writer->write($qr_code,label: $label);
        header("Content-Type: " . $result->getMimeType());

        $result->saveToFile("Ticket-".$id.".png");
    }
    /*********************** ticket en pdf **************************/

    public function enPdf(Request $request)
    {
    
       
        
    }


    /*********************** calcul Tickets**************************/
    public function getRemainingTicketsCount(Events $event): int
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ticketsRepository = $entityManager->getRepository(Tickets::class);

        // Get the total number of tickets sold for the event
        $soldTicketsCount = $ticketsRepository->createQueryBuilder('t')
            ->select('SUM(t.quantite)')
            ->andWhere('t.idevent = :idevent')
            ->setParameter('idevent', $event->getIdevent())
            ->getQuery()
            ->getSingleScalarResult();

        // Calculate the remaining tickets count
        $remainingTicketsCount = $event->getNombrelimevent() - $soldTicketsCount;

        return $remainingTicketsCount;
    }


    /***************affichage au panier*****************/
    #[Route('/ticketOfThisUser', name: 'ticketsUser', methods: ['GET'])]
    public function index3(TicketsRepository $ticketsRepository): Response
    {
       
        $tickets = $this->getDoctrine()
        ->getRepository(Tickets::class)
        ->findAll();

        $totalRevenue = $ticketsRepository->getTotalRevenue();
        return $this->render('Front/Tickets/affichageFront.html.twig', [
            'tickets' => $tickets,
            'totalRevenue'=>$totalRevenue,
        ]);
    }
    /***************affichage au panier en json *****************/
    #[Route('/ticketOfThisUserapi', name: 'ticketsUserapi', methods: ['GET'])]
    public function index3api(TicketsRepository $ticketsRepository): Response
    {
    
        $tickets = $this->getDoctrine()
        ->getRepository(Tickets::class)
        ->findAll();

        $data=[];
     foreach ($tickets as $ticket){
         $data[]=[
            'idticket'=>$ticket->getIdticket(),
        'idevent'=>$ticket->getIdevent(),
        'idUser'=>$ticket->getIdUser(),
        'login'=>$ticket->getLogin(),
        'quantite'=>$ticket->getQuantite(),
        'prixtotale'=>$ticket->getPrixtotale(),
             
         ];}
         return $this->json($data,200,['Content-Type'=>'application/json']); 
    }

    #[Route('/DeleteTicket/{id}', name: 'DeleteTicket')]
    public function DeleteTicket(ManagerRegistry $doctrine,$id): Response
    {
        $repo=$doctrine->getRepository(Tickets::class);
        $em=$doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();
        return $this->redirectToRoute('tickets');
    }
/******************delete json****************/
    #[Route('/DeleteTicketapi/{id}', name: 'DeleteTicketapi')]
    public function DeleteTicketapi(ManagerRegistry $doctrine,$id,NormalizerInterface $Normalizer): Response
    {
        $repo=$doctrine->getRepository(Tickets::class);
        $em=$doctrine->getManager();
        $ticket=$repo->find($id);
        $em->remove($ticket);
        $em->flush();
        $jsonContent =$Normalizer->normalize($ticket,'json',['groups'=>'Events']);

        return new Response("deleted".json_encode($jsonContent));
    }

    #[Route('/DeleteTicket2/{id}', name: 'DeleteTicket2')]
    public function DeleteTicket2(ManagerRegistry $doctrine,$id): Response
    {
        $repo=$doctrine->getRepository(Tickets::class);
        $em=$doctrine->getManager();
        $em->remove($repo->find($id));
        $em->flush();
        return $this->redirectToRoute('ticketsUser');
    }
}
