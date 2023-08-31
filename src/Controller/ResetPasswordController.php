<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\JWTToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTToken               $JWTToken,
        private readonly UserRepository         $userRepository,
        private readonly EmailVerifier          $emailVerifier
    )
    {
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token): Response
    {
        $secret = $this->getParameter('app_secret');

        //if ( !$this->JWTToken->check($token, $secret)) {
        //    $this->addFlash('error', 'Le token de réinitialisation de mot de passe est invalide');
        //    return $this->redirectToRoute('app_home');
        //}

        if ($this->JWTToken->isExpired($token)) {
            $this->addFlash('error', 'Le token de réinitialisation de mot de passe est expiré');
            return $this->redirectToRoute('app_home');
        }

        $payload = $this->JWTToken->getPayload($token);
        $user = $this->userRepository->findOneBy(['email' => $payload['user_email']]);

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();


            return $this->redirectToRoute('app_home');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['username' => $form->getData()]);

            $this->addFlash('info', "Un e-mail sera envoyé à cet utilisateur s'il existe.");

            if (!empty($user) && $user->isVerified()) {
                $secret = $this->getParameter('app_secret');
                $expireHours = 4;
                $token = $this->JWTToken->generateToken(['user_email' => $user->getEmail()], $secret, $expireHours);

                $this->emailVerifier->sendResetEmail($user, $token, $expireHours);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/forgot_password.html.twig', [
            'formPassword' => $form->createView()
        ]);
    }
}
