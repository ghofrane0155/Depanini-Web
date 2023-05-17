<?php

namespace App\Controller;

use App\Entity\Events;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api/{id}/edit', name: 'api_event_edit', methods:[ 'PUT' ])]
    public function  majEvent(Events $calendar, Request $request): Response
    {
         // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
           
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->end) &&
            isset($donnees->EventPrice) && !empty($donnees->end) &&
            isset($donnees->LimitedNumber) && !empty($donnees->LimitedNumber) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->organiser) && !empty($donnees->organiser) &&
            isset($donnees->place) && !empty($donnees->place)&&
            isset($donnees->Image) && !empty($donnees->end)  
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$calendar){
                // On instancie un rendez-vous
                $calendar = new Events;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            
            $calendar->setNomevent($donnees->title);
            $calendar->setDescriptionevent($donnees->description);
            $calendar->setDatedebutevent(new DateTime($donnees->start));
            $calendar->setDatefinevent(new DateTime($donnees->end));
            $calendar->setLieuevent($donnees->place);
            $calendar->setOrganisateurevent($donnees->organiser);
            $calendar->setPrixevent($donnees->EventPrice);
            $calendar->setNombrelimevent($donnees->LimetedNumber);
            $calendar->setImageevent($donnees->Image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();
            // On retourne le code
            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Données youssef incomplètes', 404);
        }

        return $this->render('Back/api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
