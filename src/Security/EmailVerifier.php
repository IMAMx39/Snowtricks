<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailVerifier
{

    public function __construct(private MailerInterface $mailer,)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmationByToken(User $user, string $token, int $expireHours): void
    {
        $email = (new TemplatedEmail())
            ->from('p6-snowtricks@oc.fr')
            ->to($user->getEmail())
            ->subject('Please Confirm your Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
                'expireHours' => $expireHours,
            ]);
        $this->mailer->send($email);
    }
}