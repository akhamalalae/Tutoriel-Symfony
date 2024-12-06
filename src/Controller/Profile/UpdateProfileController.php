<?php

namespace App\Controller\Profile;

use App\Entity\User;
use App\Services\File\FileUploader;
use App\Repository\UserRepository;
use App\Form\Type\Profile\UpdateProfileFormType;
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
use Symfony\Component\Security\Core\Security;

class UpdateProfileController extends AbstractController
{
    const DIRECTORY_AVATARS = 'img/avatars'; 
    private User $currentLoggedUser;

    public function __construct(
        Security $security)
    {
        $this->currentLoggedUser = $security->getUser();
    }

    #[Route('/update/profile', name: 'app_update_profile')]
    public function update(
        Request $request,
        FileUploader $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager): Response
    {
        $user = $this->currentLoggedUser;
        
        $form = $this->createForm(UpdateProfileFormType::class, $user);
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
                $fileName = $fileUploader->upload($file, self::DIRECTORY_AVATARS);
                $user->setBrochureFilename($fileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your profile has been updated.');

            //return $this->redirectToRoute('app_update_profile', array('id' => $id));
            return $this->redirectToRoute('app_update_profile');
        }

        return $this->render('profile/update_profile.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }
}
