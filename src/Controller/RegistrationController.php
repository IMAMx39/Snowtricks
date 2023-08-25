<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\JWTToken;
use App\Service\ManagerFile;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{

    public function __construct(private readonly EmailVerifier $emailVerifier, private readonly JWTToken $tokenJWT)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        ManagerFile                 $managerFile,
    ): Response
    {
        $secret = $this->getParameter('app_secret');
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $newFilename = $managerFile->uploadAvatar($avatarFile);
                $user->setAvatarFileName($newFilename);
            }
            $user->setCreatedAt(new DateTimeImmutable);
            $user->setIsVerified(false);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $expireHours = 4;
            $token = $this->tokenJWT->generateToken(['user_email' => $user->getEmail()], $secret, $expireHours);

            $this->emailVerifier->sendEmailConfirmationByToken($user, $token, $expireHours);

            $this->addFlash('info', $user->getUsername() . " votre compte à été bien creer, veuillez vous connecter");

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(string $token, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {

        $payload = $this->tokenJWT->getPayload($token);

        $user = $userRepository->findOneBy(['email' => $payload['user_email']]);
        if (!$user || $user->isVerified()) {
            $this->addFlash('error', 'L\'utilisateur est déjà vérifié ou est invalide');
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $manager->persist($user);
        $manager->flush();

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a bien été validée !');

        return $this->redirectToRoute('app_home');
    }
}
