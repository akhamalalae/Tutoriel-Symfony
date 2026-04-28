<?php

namespace App\Controller\Profil;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Type\User\UserFormType;
use App\Services\File\FileUploader;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\Breadcrumb\BreadcrumbService;
use App\Services\User\UserService;
use App\UseCase\Autocomplete\FindUsersAssignedToUseCase;

#[Route('/manager/users')]
class ProfilsController extends AbstractController
{
    const DIRECTORY_AVATARS = 'img/avatars'; 

    public function __construct(
        private UserService $userService,
        private FileUploader $fileUploader,
        private TranslatorInterface $translator,
        private BreadcrumbService $breadcrumbService,
        private EntityManagerInterface $entityManager,
        private FindUsersAssignedToUseCase $findUsersAssignedToUseCase,
    ) {}

    #[Route('/', name: 'users_list', options: ['expose' => true])]
    public function list(
        Request $request,
        UserRepository $repo,
        PaginatorInterface $paginator
    ): Response {
        $this->breadcrumbService->addBreadcrumb('user.label', $this->generateUrl('users_list'));

        $search = $request->query->get('search') ?? '';

        $pagination = $paginator->paginate(
            $this->findUsersAssignedToUseCase->execute($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('profil/_list.html.twig', [
            'users' => $pagination,
        ]);
    }

    #[Route('/form/{id}', name: 'users_form', defaults: ['id' => null], options: ['expose' => true])]
    public function form(?User $user = null): Response
    {
        $view = $user ? 'update' : 'register';

        $form = $this->createForm(UserFormType::class, $user ?? new User(), [
            'view' => 'all',
        ]);

        return $this->render('profil/_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/user/save/{id}', name: 'users_save', methods: ['POST'], defaults: ['id' => null], options: ['expose' => true])]
    public function save(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $repo,
        ?int $id = null
    ): JsonResponse {
    
        $user = $id ? $repo->find($id) : new User();
        dump($id, $user);
    
        $form = $this->createForm(UserFormType::class, $user, [
            'action' => $this->generateUrl('users_save', ['id' => $id]),
            'view' => 'all',
        ]);
    
        $form->handleRequest($request);
    
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json([
                'success' => false,
                'errors' => (string) $form->getErrors(true, false),
            ]);
        }

        $file = $form->get('image')->getData();
        if ($file) {
            $mimeType = $file->getMimeType();
            
            $fileName = $this->fileUploader->upload($file, self::DIRECTORY_AVATARS)['name'];

            $user->setBrochureFilename($fileName);

            $user->setMimeType($mimeType);
        }

        $roles = $form->get('roles')->getData();
        dump($roles);
        if ($roles) {
            $user->setRoles($roles);
        }

        $plainPassword = $form->get('plainPassword')->getData();

        if (!$id) {
            if (!$plainPassword) {
                return $this->json([
                    'success' => false,
                    'message' => 'Password required'
                ]);
            }

            $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
            $em->persist($user);

        } else {
            if ($plainPassword) {
                $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
            }
            // sinon: on ne touche PAS au password existant (Doctrine le garde automatiquement)
        }
            
        $em->flush();
    
        return $this->json([
            'success' => true,
            'message' => $id ? 'User updated' : 'User created'
        ]);
    }

    #[Route('/delete/{id}', name: 'users_delete', methods: ['POST'], options: ['expose' => true])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($user);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'User deleted'
            ]);

        } catch (\Throwable $e) {

            return $this->json([
                'success' => false,
                'message' => 'Delete failed'
            ], 500);
        }
    }
}