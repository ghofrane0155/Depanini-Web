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

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
class EventsController extends AbstractController
{
    #[Route('/excel/generate', name: 'excel')]
    public function generateExcel(EventsRepository $eventRepository,TicketsRepository $ticketsRepository): Response
    {
        // Fetch data from database
        $Events = $eventRepository->findAll();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        

        

        // Set the cell values
        $sheet->setCellValue('A1', 'Event Name')->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ccccff');
        $sheet->setCellValue('B1', 'Start Time')->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('b3b3ff');
           $sheet->setCellValue('C1', 'End Time')->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('9999ff');
           $sheet->setCellValue('D1', 'organiser')->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('8080ff');
             $sheet->setCellValue('E1', 'place')->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('6666ff');
             $sheet->setCellValue('F1', 'price')->getStyle('F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4d4dff');
              

        // Set the data in the appropriate cells
        $row = 2;
        foreach ($Events as $event) {
            $sheet->setCellValue('A' . $row, $event->getNomevent())
                  ->setCellValue('B' . $row, $event->getDatedebutevent()->format('Y-m-d H:i:s'))
                  ->setCellValue('C' . $row, $event->getDatefinevent()->format('Y-m-d H:i:s'))
                  ->setCellValue('D' . $row, $event->getOrganisateurevent())
                  ->setCellValue('E' . $row, $event->getLieuevent())
                  ->setCellValue('F' . $row, $event->getPrixevent());
            $row++;
            
        }
        $totalRevenue = $ticketsRepository->getTotalRevenue();
        $sheet->setCellValue('G' . ($row + 1), 'Total Income')
          ->setCellValue('G' . ($row + 2), $totalRevenue. ' DT');
          $sheet->getStyle('G' . ($row + 2))->getFont()->setSize(13)->setBold(true)->setName('Arial');
          $sheet->getStyle('G' . ($row + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('9966ff');

          // Set the background color and font color of the header row
        $headerStyle = $sheet->getStyle('A1:F1');
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('668CFF');
        $headerStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $headerStyle->getFont()->setSize(14)->setBold(true)->setName('Arial'); 

        // Set the font size and boldness of the cell values
        $dataStyle = $sheet->getStyle('A1:F1' . $row);
        $dataStyle->getFont()->setSize(12)->setName('Arial');
        $dataStyle1 = $sheet->getStyle('A2:F' . $row);
        $dataStyle1->getFont()->setSize(12)->setBold(true)->setName('Arial');

        // Auto-size the columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add a border to the data cells
        $borderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '330099'],
                ],
            ],
        ];
        $dataStyle->applyFromArray($borderStyle);

        // Create a new Excel writer object
        $writer = new Xlsx($spreadsheet);

        // Set the output file name
        $outputFileName = 'events.xlsx';

        // Save the Excel file to the output file name
        $writer->save($outputFileName);

        // Return the Excel file as a response to the client
        return $this->file($outputFileName, 'events.xlsx');
    }

    /*****************affichage**************/
    #[Route('/events', name: 'events2')]
    public function index(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
        $Events=$doctrine->getRepository(Events::class)->findAll();
        $entityManager = $doctrine->getManager();

        // Retrieve the data for the line chart
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

        return $this->render('Back/Events_list/index.html.twig', [
            'Events' => $Events,
            'totalRevenue' => $totalRevenue,
            'totalEvents' => $totalEvents,
            'totalTickets' => $totalTickets,
            'chartData' => $chartData,
        ]);
    }
    #[Route('/eventsapi', name: 'events')]
    public function indexapi(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
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
    
   
    /******************delete****************/
    #[Route('/DeleteEvent/{id}', name: 'DeleteEvent')]
public function DeleteEvent(ManagerRegistry $doctrine,$id): Response
{
    $repo=$doctrine->getRepository(Events::class);
    $em=$doctrine->getManager();
    $em->remove($repo->find($id));
    $em->flush();

    return $this->redirectToRoute('events');
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


    /************ find by nom event (search) *********/
   
    public function searchByName(Request $request, EventsRepository $repository)
    {
          $events = $repository->findAll();

        if ($request->isMethod("post")) {
            
            $nomEvent =$request->get('nomEvent');
            $resultOfSearch = $repository->findEventByName($nomEvent);
            return $this->render('Back/create_event/details.html.twig', [
                'events' => $resultOfSearch]);
        }

        return $this->render("student/read.html.twig",
            ["events"=>$events]);
    }

    
    /******************Update****************/
#[Route('/updateEvent/{id}', name: 'updateEvent')]
    public function updateEventById(EventsRepository $ev,ManagerRegistry $doctrine,$id, Request $request, EntityManagerInterface $em): Response
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
    
            // // redirect to the event list page
            return $this->redirectToRoute('events');
        }
        
        // dd($form->getErrors(true));
        return $this->render('Back/create_event/update.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    /******************Update json****************/
#[Route('/updateEventapi/{id}', name: 'updateEventapi')]
public function updateEventByIdapi(EventsRepository $ev,ManagerRegistry $doctrine,$id, Request $req, EntityManagerInterface $em,NormalizerInterface $Normalizer): Response
{
    
    $em = $this->getDoctrine()->getManager();
    $event=$ev->find($id);
    $datedebutevent = new \DateTime($req->get('datedebutevent'));
    $datefinevent = new \DateTime($req->get('datefinevent'));
    $event->setNomevent($req->get('nomevent'));
    $event->setLieuevent($req->get('lieuevent'));
    $event->setDescriptionevent($req->get('descriptionevent'));
    $event->setOrganisateurevent($req->get('organisateurevent'));
    $event->setPrixevent($req->get('prixevent'));
    $event->setNombrelimevent($req->get('nombrelimevent'));
    $event->setDatedebutevent($datedebutevent);
    $event->setDatefinevent($datefinevent);
    $event->setImageevent($req->get('imageevent'));
    $em->flush();
   // $em->find(Events::class,$id);
    
    
     // Return a success response
     return $this->json(['message' => 'Feedback updated successfully']);
       
    
    
}
    /*****************fetch the last three events based on their id***************/
     
    #[Route('/ev', name: 'Events')]
    public function fetch3(EntityManagerInterface $entityManager): Response
    {
        // Get the last three events based on their ID
        $events = $entityManager->getRepository(Events::class)
            ->findBy([], ['idevent' => 'DESC'], 3);

        return $this->render('Front/EventListUser/index.html.twig', [
            'events' => $events,
        ]);
    }
    /***************fetch all to user (see more button)********************/
    /*****************affichage**************/
    #[Route('/FetchEvents', name: 'ReadAllEvents')]
    public function fetch(ManagerRegistry $doctrine): Response
    {

        $currentDate = new \DateTime();

        // $Events=$doctrine->getRepository(Events::class)->findAll();
        $Events=$doctrine->getRepository(Events::class)->createQueryBuilder('e')
        ->where('e.datedebutevent > :currentDate')
        ->setParameter('currentDate', $currentDate)
        ->getQuery()
        ->getResult();
        
        return $this->render('Front/EventListUser/eventList.html.twig', [
            'Events' => $Events,
        ]);
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

 #[Route('/Chartapi', name: 'Chartapi')]
    public function indexapi1(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $ticketsRepository = $entityManager->getRepository(Tickets::class);
        $query = $ticketsRepository->createQueryBuilder('t')
        ->select('e.nomevent, SUM(t.prixtotale) AS totalPrix')
        ->leftJoin('t.idevent', 'e')
        ->groupBy('t.idevent')
        ->getQuery();

    $chartData = $query->getResult();
    foreach ($chartData as $data) {
        $data['totalPrix'] = intval($data['totalPrix']);
    }


    // Encode the data as JSON
    $jsonData = json_encode($chartData);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }

    #[Route('/totalRevenue', name: 'totalRevenue')]
    public function totalRevenue(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
    $totalRevenue = $ticketsRepository->getTotalRevenue();

   
    // Encode the data as JSON
    $jsonData = json_encode($totalRevenue);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }

    #[Route('/totalEvents', name: 'totalEvents')]
    public function totalEvents(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
    {
        $totalEvents = $doctrine->getManager()->createQueryBuilder()
        ->select('COUNT(e.idevent)')
        ->from(Events::class, 'e')
        ->getQuery()
        ->getSingleScalarResult();
   
    // Encode the data as JSON
    $jsonData = json_encode($totalEvents);

    // Create a new response object with the JSON data
    $response = new Response($jsonData);

    // Set the content type header to JSON
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    }
    #[Route('/totalTickets', name: 'totalTickets')]
        public function totalTickets(ManagerRegistry $doctrine,TicketsRepository $ticketsRepository,EventsRepository $eventsRepository): Response
        {
            $totalTickets = $ticketsRepository->count([]);
        // Encode the data as JSON
        $jsonData = json_encode($totalTickets);

        // Create a new response object with the JSON data
        $response = new Response($jsonData);

        // Set the content type header to JSON
        $response->headers->set('Content-Type', 'application/json');

        return $response;
        }

        #[Route('/eventstat', name: 'eventstat')]
public function eventstat(ManagerRegistry $doctrine, TicketsRepository $ticketsRepository, EventsRepository $eventsRepository): Response
{
    $Events = $doctrine->getRepository(Events::class)->findAll();
    $entityManager = $doctrine->getManager();

    // Retrieve the data for the line chart
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
        'Events' => $Events,
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

    
}
