<?php

namespace App\Controller\Profile;

use App\Entity\User;
use App\Services\File\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\Type\User\UserFormType;
use App\Services\Breadcrumb\BreadcrumbService;
use App\Services\User\UserService;

class UpdateProfileController extends AbstractController
{
    const DIRECTORY_AVATARS = 'img/avatars'; 

    public function __construct(
        private UserService $userService,
        private FileUploader $fileUploader,
        private TranslatorInterface $translator,
        private BreadcrumbService $breadcrumbService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/user/update/profil', name: 'app_update_profil')]
    public function update(Request $request): Response
    {
        $user = $this->userService->getAuthenticatedUser();

        $this->breadcrumbService->addBreadcrumb('Update profil', $this->generateUrl('app_update_profil'));

        $form = $this->createForm(UserFormType::class, $user, [
            'view' => 'update',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $mimeType = $file->getMimeType();
                
                $fileName = $this->fileUploader->upload($file, self::DIRECTORY_AVATARS)['name'];

                $user->setBrochureFilename($fileName);

                $user->setMimeType($mimeType);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            $this->addFlash('success', $this->translator->trans('Your profile has been updated'));

            return $this->redirectToRoute('app_update_profil');
        }

        return $this->render('profil/update_profil.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }
}
