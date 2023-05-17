<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('Front/base.html.twig');
    }
   

    #[Route('/home', name: 'about')]
    public function home(): Response
    {
        return $this->render('Front/visiteurpages/base.html.twig');
    }

    #[Route('/welcome', name: 'visit')]
    public function hey(): Response
    {
        
    
        return $this->render('Front/visiteurpages/welcome.html.twig',);
    }
    
}
