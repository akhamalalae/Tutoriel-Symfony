<?php

namespace App\Controller\Registration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\Type\Registration\ForgotPasswordType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token sécurisé
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour')); // Token valide 1h
                $entityManager->flush();

                // Envoyer un email avec le lien de réinitialisation
                $resetLink = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $email = (new Email())
                    ->from('akhamal.alae@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html('<p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe : <a href="' . $resetLink . '">Réinitialiser mon mot de passe</a></p>');

                $mailer->send($email);
dump($resetLink);
                $this->addFlash('success', 'Un email a été envoyé avec les instructions pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
            }
        }

        return $this->render('registration/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
