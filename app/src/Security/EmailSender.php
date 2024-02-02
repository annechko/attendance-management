<?php

namespace App\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail,
    ) {
    }

    public function sendEmailOnRegistration(\App\Entity\Teacher $teacher): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, 'Admin'))
            ->to($teacher->getEmail())
            ->subject('You have been registered')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $context = $email->getContext();
        $context['password'] = $teacher->getFirstTimePassword();

        $email->context($context);

        $this->mailer->send($email);
    }
}
