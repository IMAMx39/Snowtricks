<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class EmailVerifier
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
            ->from('no-replayd@snowtricks.com')
            ->to($user->getEmail())
            ->subject('Confirmation de votre compte')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
                'expireHours' => $expireHours,
            ]);
        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendResetEmail(User $user, string $token, int $expireHours): void
    {
        $email = (new TemplatedEmail())
            ->from('reset_password@snowtricks.com')
            ->to($user->getEmail())
            ->subject('Demande de réinitialisation du mot de passe')
            ->htmlTemplate('reset_password/reset_password_email.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
                'expireHours' => $expireHours,
            ]);
        $this->mailer->send($email);
    }
}