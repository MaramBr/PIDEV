<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $body)
    {
        $email = (new Email())
        ->from('rayan.lahmar@esprit.tn')
        ->to('rayan.lahmar@esprit.tn')
        ->subject('Nouveau message')
        ->text('Bonjour, voici un nouveau message !');
        $this->mailer->send($email);

    }
}
