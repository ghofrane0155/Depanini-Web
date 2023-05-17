<?php

namespace App\Controller;

use App\Form\ForgotPassType;
use App\Form\ForgotPasswordType;
use App\Service\TwilioClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class smsController extends AbstractController
{
/************************************************************************** */
    #[Route('/send-sms', name: 'Password_send_sms', methods: ['GET'])]
    public function sendSms(Request $request, TwilioClient $twilioClient, EntityManagerInterface $entityManager)
    {

        $smsController = new smsController();
        $form = $this->createForm(ForgotPasswordType::class);

         $form->handleRequest($request);
        
         $to = '+216'."53414061"; // The phone number to send the SMS to
         $from = '+13159037947'; // Your Twilio phone number
         $body = "Someone try to change your password. If you think someone else knows or has changed your password, please contact our support team immediately." ;// The message body

         $twilioClient->sendSMS($to, $from, $body);       
       
         return $this->redirectToRoute('app_login');

        //return new Response('SMS sent successfully!');
    }
}