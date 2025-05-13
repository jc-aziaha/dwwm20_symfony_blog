<?php
namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

    class EmailSenderService
    {
        public function __construct(
            private MailerInterface $mailer
        )
        { 
        }

        /**
         * Cette méthode permet d'envoyer l'email
         *
         * @param array $data
         * @return void
         */
        public function sendEmail(array $data = []): void
        {
            $senderEmail        = $data['sender_email'];
            $senderFullName     = $data['sender_full_name'];
            $recipientEmail     = $data['recipient_email'];
            $subject            = $data['subject'];
            $htmlTemplate       = $data['html_template'];
            $context            = $data['context'];

            $email = (new TemplatedEmail())
                ->from(new Address($senderEmail, $senderFullName))
                ->to((string) $recipientEmail)
                ->subject($subject)
                ->htmlTemplate($htmlTemplate)
                ->context($context)
            ;

            try 
            {
                $this->mailer->send($email);
            } 
            catch (TransportExceptionInterface $th) 
            {
                throw $th;
            }
        }
    }