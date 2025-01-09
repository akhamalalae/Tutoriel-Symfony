<?php

namespace App\Controller\Registration;

use App\Entity\User;
use App\Form\Type\Registration\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Services\File\FileUploader;
use Symfony\Component\Mime\Email;
use App\Form\Type\Registration\ResetPasswordType;
class RegistrationController extends AbstractController
{
    const DIRECTORY_AVATARS = 'img/avatars'; 

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        MailerInterface $mailer,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        TranslatorInterface $translator): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'view' => 'register',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $file = $form->get('image')->getData();
            if ($file) {
                $mimeType = $file->getMimeType();
                $fileName = $fileUploader->upload($file, self::DIRECTORY_AVATARS)['name'];
                $user->setBrochureFilename($fileName);
                $user->setMimeType($mimeType);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('akhamal.alae@gmail.com', 'Mail'))
                    ->to($user->getEmail())
                    ->subject($translator->trans('Please Confirm your Email'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
