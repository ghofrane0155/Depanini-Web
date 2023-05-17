<?php
namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class mail extends AbstractController
{
    
    private $mailer;
    
    
    public function __construct( MailerInterface $mailer)
     {
        
        $this->mailer=$mailer;
     }
    
    public function sendEmail($to,$subj,$templatePath, $templateData): void
    {
        
        $email = (new Email())
            ->from('depanini2023@gmail.com')
            ->to($to)
            ->subject($subj)
            ->html(
                $this->renderView(
                    $templatePath,
                    $templateData
                )
            );
            //->text($post);
             
            $this->mailer->send($email);
      
        // ...
    }
}
?>