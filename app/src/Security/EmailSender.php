<?php

namespace App\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

class EmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail,
    ) {
    }

    public function sendEmailOnRegistration(UserInterface $user, string $password): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, 'Admin'))
            ->to($user->getUserIdentifier())
            ->subject('You have been registered')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $context = $email->getContext();
        $context['password'] = $password;

        $email->context($context);

        $this->mailer->send($email);
    }
}
