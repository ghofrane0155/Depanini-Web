<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\EventFormType;
use App\Repository\EventsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// /**
//  * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
//  */
class CreateEventController extends AbstractController
{
    /******************create*********************/
// /**
//  * @IsGranted("ROLE_ADMIN")
//  */
    #[Route('/events/create', name: 'CreateEvent')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
    $event = new Events();
    $form = $this->createForm(EventFormType ::class, $event);


    $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();
            if ($image) {
                $imageString = $image->getClientOriginalName();
            }
            $event->setImageevent("Back/images/" . $imageString);
            // Persist the event to the database
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'The Event is created successfully!');
            return $this->redirectToRoute('events', ['id' => $event->getIdevent()]); 
        }      
   

        return $this->render('Back/create_event/index.html.twig', [
            'form' => $form->createView(),
            'formErrors' => $form->getErrors(true, false)
        ]);
    }

    #[Route('/events/createapi', name: 'CreateEventapi')]
    public function indexapi(Request $req,NormalizerInterface $Normalizer,EntityManagerInterface $entityManager): Response
    {
        
    $event = new Events();
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

    $entityManager=$this->getDoctrine()->getManager();
            // Persist the event to the database
    $entityManager->persist($event);
    $entityManager->flush();
            
    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($event);

    return new JsonResponse($formatted);
        
    }

    

    
}
