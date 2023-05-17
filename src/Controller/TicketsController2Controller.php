<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Form\Tickets1Type;
use App\Repository\TicketsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
#[Route('/tickets/controller2')]
class TicketsController2Controller extends AbstractController
{
    #[Route('/hey', name: 'app_tickets_controller2_index', methods: ['GET'])]
    public function index(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets_controller2/index.html.twig', [
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_tickets_controller2_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketsRepository $ticketsRepository): Response
    {
        $ticket = new Tickets();
        $form = $this->createForm(Tickets1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketsRepository->save($ticket, true);

            return $this->redirectToRoute('app_tickets_controller2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets_controller2/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{idticket}', name: 'app_tickets_controller2_show', methods: ['GET'])]
    public function show(Tickets $ticket): Response
    {
        return $this->render('tickets_controller2/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{idticket}/edit', name: 'app_tickets_controller2_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tickets $ticket, TicketsRepository $ticketsRepository): Response
    {
        $form = $this->createForm(Tickets1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketsRepository->save($ticket, true);

            return $this->redirectToRoute('app_tickets_controller2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets_controller2/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{idticket}', name: 'app_tickets_controller2_delete', methods: ['POST'])]
    public function delete(Request $request, Tickets $ticket, TicketsRepository $ticketsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getIdticket(), $request->request->get('_token'))) {
            $ticketsRepository->remove($ticket, true);
        }

        return $this->redirectToRoute('app_tickets_controller2_index', [], Response::HTTP_SEE_OTHER);
    }
}
