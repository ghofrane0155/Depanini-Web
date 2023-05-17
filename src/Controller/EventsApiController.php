<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Tickets;
use App\Form\EventDetailsFormType;
use App\Form\TicketsType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EventFormType;
use App\Repository\EventsRepository;
use App\Repository\TicketsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class EventsJsonController extends AbstractController
{
    // #[Route('/Chartapi', name: 'events')]
    // public function indexapi(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    // {
    //     $entityManager = $doctrine->getManager();
    //     $ticketsRepository = $entityManager->getRepository(Tickets::class);
    //     $query = $ticketsRepository->createQueryBuilder('t')
    //     ->select('e.nomevent, SUM(t.prixtotale) AS totalPrix')
    //     ->leftJoin('t.idevent', 'e')
    //     ->groupBy('t.idevent')
    //     ->getQuery();

    // $chartData = $query->getResult();
    // foreach ($chartData as &$data) {
    //     $data['totalPrix'] = intval($data['totalPrix']);
    // }


    // // Encode the data as JSON
    // $jsonData = json_encode($chartData);

    // // Create a new response object with the JSON data
    // $response = new Response($jsonData);

    // // Set the content type header to JSON
    // $response->headers->set('Content-Type', 'application/json');

    // return $response;
    // }
  
    #[Route('/eventsapi', name: 'events')]
    public function totalRevenue(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
    $totalRevenue = $ticketsRepository->getTotalRevenue();

   

    // Create an associative array containing the data
    $data = [
        'totalRevenue' => $totalRevenue,
      
    ];

    // Encode the data as JSON
    $jsonData = json_encode($data);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }
    #[Route('/eventsapi', name: 'events')]
    public function indexapi3(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $ticketsRepository = $entityManager->getRepository(Tickets::class);
        $query = $ticketsRepository->createQueryBuilder('t')
        ->select('e.nomevent, SUM(t.prixtotale) AS totalPrix')
        ->leftJoin('t.idevent', 'e')
        ->groupBy('t.idevent')
        ->getQuery();

    $chartData = $query->getResult();
    foreach ($chartData as &$data) {
        $data['totalPrix'] = intval($data['totalPrix']);
    }

    $totalRevenue = $ticketsRepository->getTotalRevenue();

    $totalEvents = $doctrine->getManager()->createQueryBuilder()
        ->select('COUNT(e.idevent)')
        ->from(Events::class, 'e')
        ->getQuery()
        ->getSingleScalarResult();

    $totalTickets = $ticketsRepository->count([]);

    // Create an associative array containing the data
    $data = [
        'totalRevenue' => $totalRevenue,
        'totalEvents' => $totalEvents,
        'totalTickets' => $totalTickets,
        'chartData' => $chartData,
    ];

    // Encode the data as JSON
    $jsonData = json_encode($data);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }
    #[Route('/eventsapi', name: 'events')]
    public function indexapi4(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $ticketsRepository = $entityManager->getRepository(Tickets::class);
        $query = $ticketsRepository->createQueryBuilder('t')
        ->select('e.nomevent, SUM(t.prixtotale) AS totalPrix')
        ->leftJoin('t.idevent', 'e')
        ->groupBy('t.idevent')
        ->getQuery();

    $chartData = $query->getResult();
    foreach ($chartData as &$data) {
        $data['totalPrix'] = intval($data['totalPrix']);
    }

    $totalRevenue = $ticketsRepository->getTotalRevenue();

    $totalEvents = $doctrine->getManager()->createQueryBuilder()
        ->select('COUNT(e.idevent)')
        ->from(Events::class, 'e')
        ->getQuery()
        ->getSingleScalarResult();

    $totalTickets = $ticketsRepository->count([]);

    // Create an associative array containing the data
    $data = [
        'totalRevenue' => $totalRevenue,
        'totalEvents' => $totalEvents,
        'totalTickets' => $totalTickets,
        'chartData' => $chartData,
    ];

    // Encode the data as JSON
    $jsonData = json_encode($data);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }
 
/******************delete json****************/
    #[Route('/DeleteEventapi/{id}', name: 'DeleteEventapi')]
    public function DeleteEventapi(ManagerRegistry $doctrine,$id,NormalizerInterface $Normalizer): Response
    {
        $repo=$doctrine->getRepository(Events::class);
        $em=$doctrine->getManager();
        $event=$repo->find($id);
        $em->remove($event);
        $em->flush();
    $jsonContent =$Normalizer->normalize($event,'json',['groups'=>'Events']);

        return new Response("deleted".json_encode($jsonContent));
    }

    /******************Details****************/
    #[Route('/detailsEvent/{id}', name: 'detailsEvent')]
    public function detailsEventById($id, EntityManagerInterface $em): response
    {
        $em = $this->getDoctrine()->getManager();
        $event=$em->find(Events::class,$id);
        $form = $this->createForm(EventDetailsFormType::class, $event);
        // dd($request->request->all());
        // dd($form->getErrors(true));
        return $this->render('Back/create_event/details.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    /******************Details json****************/
    #[Route('/detailsEventapi/{id}', name: 'detailsEventapi')]
    public function detailsEventByIdapi($id, EntityManagerInterface $em): response
    {
        $em = $this->getDoctrine()->getManager();
        $event=$em->find(Events::class,$id);
        $form = $this->createForm(EventDetailsFormType::class, $event);
        $data=[];
     
         $data[]=[
             'idevent'=>$event->getIdevent(),
             'nomevent'=>$event->getNomevent(),
             'datedebutevent'=>$event->getDatedebutevent()->format('Y-m-d'),
             'datefinevent'=>$event->getDatefinevent()->format('Y-m-d'),
             'descriptionevent'=>$event->getDescriptionevent(),
             'organisateurevent'=>$event->getOrganisateurevent(),
             'prixevent'=>$event->getPrixevent(),
             'imageevent'=>$event->getImageevent(),
             'lieuevent'=>$event->getLieuevent(),
             'nombrelimevent'=>$event->getNombrelimevent(),
         ];
        
         return $this->json($data,200,['Content-Type'=>'application/json']);
        
    }



    /******************Update json****************/
#[Route('/updateEventapi/{id}', name: 'updateEventapi')]
public function updateEventByIdapi(EventsRepository $ev,ManagerRegistry $doctrine,$id, Request $request, EntityManagerInterface $em,NormalizerInterface $Normalizer): Response
{
    
   // $em = $this->getDoctrine()->getManager();
    $event=$ev->find($id);
   // $em->find(Events::class,$id);
    $form = $this->createForm(EventFormType::class, $event);
    $form->handleRequest($request);
    if ($form->isSubmitted() ) {
        
        $event= $form->getData();
        
        // persist changes to database
        $em = $doctrine->getManager();
        $em->persist($event);
        $em->flush();

        $jsonContent=$Normalizer->normalize($event,'json',['groups'=>'Events']);
        return $this->json($jsonContent,200,['Content-Type'=>'application/json']);  
    }
    $jsonContent=$Normalizer->normalize($event,'json',['groups'=>'Events']);
        return $this->json($jsonContent,200,['Content-Type'=>'application/json']);
    
    
}
 
    
 /******************json*******************/
 #[Route('/FetchEventsapi', name: 'ReadAllEventsapi')]
 public function fetchapi(ManagerRegistry $doctrine) 
 {

     $currentDate = new \DateTime();

     // $Events=$doctrine->getRepository(Events::class)->findAll();
     $Events=$doctrine->getRepository(Events::class)->createQueryBuilder('e')
     ->where('e.datedebutevent > :currentDate')
     ->setParameter('currentDate', $currentDate)
     ->getQuery()
     ->getResult();
     
     $data=[];
     foreach ($Events as $event){
         $data[]=[
             'idevent'=>$event->getIdevent(),
             'nomevent'=>$event->getNomevent(),
             'datedebutevent'=>$event->getDatedebutevent()->format('Y-m-d'),
             'datefinevent'=>$event->getDatefinevent()->format('Y-m-d'),
             'descriptionevent'=>$event->getDescriptionevent(),
             'organisateurevent'=>$event->getOrganisateurevent(),
             'prixevent'=>$event->getPrixevent(),
             'imageevent'=>$event->getImageevent(),
             'lieuevent'=>$event->getLieuevent(),
             'nombrelimevent'=>$event->getNombrelimevent(),
         ];
        
     }

     return $this->json($data,200,['Content-Type'=>'application/json']);
    
 }
    
}
