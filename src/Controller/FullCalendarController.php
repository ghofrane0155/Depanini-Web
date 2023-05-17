<?php

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class FullCalendarController extends AbstractController
{
    #[Route('/fullcalendar', name: 'app_full_calendar')]
    public function index(EventsRepository $calendar): Response
    {
        $events= $calendar->findAll();
        
        $rdvs= [];
        foreach($events as $event){
            $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT); // Generate a random color
            $rdvs[]= [
                'id'=> $event->getIdevent(),
                'start' => $event->getDatedebutevent()->format('Y-m-d H:i:s'),
                'end' => $event->getDatefinevent()->format('Y-m-d H:i:s'),
                'title' => $event->getNomevent(),
                'description' => $event->getDescriptionevent(),
                'organiser' => $event->getOrganisateurevent(),
                'place' => $event->getLieuevent(),
                'LimitedNumber'=>$event->getNombrelimevent(),
                'Image'=>$event->getImageevent(),
                'EventPrice'=>$event->getPrixevent(),
                'backgroundColor' => $color, // Set the background color of the event to the random color
                'textColor' => '#FFFFFF' // Set the text color of the event
            ]; 
        }
        $data = json_encode($rdvs);
        return $this->render('Back/full_calendar/index.html.twig', compact('data'));
    }
}
