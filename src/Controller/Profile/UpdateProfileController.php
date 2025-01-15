<?php

namespace App\Controller\Profile;

use App\Entity\User;
use App\Services\File\FileUploader;
use App\Repository\UserRepository;
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
use App\Form\Type\Registration\RegistrationFormType;
use App\Services\Breadcrumb\BreadcrumbService;

class UpdateProfileController extends AbstractController
{
    const DIRECTORY_AVATARS = 'img/avatars'; 

    private User $currentLoggedUser;

    public function __construct(Security $security)
    {
        $this->currentLoggedUser = $security->getUser();
    }

    #[Route('/user/update/profil', name: 'app_update_profil')]
    public function update(
        Request $request,
        FileUploader $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher, 
        TranslatorInterface $translator,
        BreadcrumbService $breadcrumbService,
        EntityManagerInterface $entityManager): Response
    {
        $user = $this->currentLoggedUser;

        $breadcrumbService->addBreadcrumb('Update profil', $this->generateUrl('app_update_profil'));

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'view' => 'update',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            /*
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            */

            $file = $form->get('image')->getData();
            if ($file) {
                $mimeType = $file->getMimeType();
                
                $fileName = $fileUploader->upload($file, self::DIRECTORY_AVATARS)['name'];

                $user->setBrochureFilename($fileName);

                $user->setMimeType($mimeType);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans('Your profile has been updated'));

            //return $this->redirectToRoute('app_update_profil');
        }

        return $this->render('profil/update_profil.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }
}
