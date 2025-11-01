<?php

namespace App\Controller\Messaging\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Discussion\DiscussionFormType;
use App\Services\Discussion\DiscussionService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Discussion;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\User\UserService;

#[IsGranted('ROLE_USER')]
class DiscussionController extends AbstractController
{
    public function __construct(
        private DiscussionService $discussionService,
        private BreadcrumbService $breadcrumbService,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private Environment $environment
    ) {}

    #[Route('/user/discussion', name: 'app_discussion')]
    public function index(Request $request): Response
    {      
        $this->breadcrumbService->addBreadcrumb('Discussion', $this->generateUrl('app_discussion'));

        $discussion = new Discussion();
        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);
        $discussionForm->handleRequest($request);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $this->discussionService->handleDiscussionFormData($discussionForm);
        }

        return $this->render('discussion/index.html.twig', [
            'formDiscussion' => $discussionForm->createView()
        ]);
    }

    #[Route('/user/delete/discussion/{id}', name: 'app_delete_discussion', methods: ['DELETE'], options: ['expose' => true])]
    public function delete(int $id): JsonResponse
    {
        $discussion = $this->em->getRepository(Discussion::class)->find($id);

        if (!$discussion) {
            return new JsonResponse(['message' => 'Message not found'], 404);
        }

        $discussionMessageUsers = $discussion->getDiscussionMessageUsers();
        foreach ($discussionMessageUsers as $item) {
            $this->em->remove($item);
        }

        $this->em->remove($discussion);
        $this->em->flush();

        return new JsonResponse(['message' => $this->translator->trans('Element deleted successfully')], 200);
    }

    #[Route('/user/delete/search/discussion/{id}', name: 'app_delete_search_discussion', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteSearchDiscussion(int $id): JsonResponse
    {
        try {
            $searchDiscussion = $this->em->getRepository(SearchDiscussion::class)->find($id);

            $this->em->remove($searchDiscussion);
            $this->em->flush();

            return new JsonResponse(['message' => $this->translator->trans('Element deleted successfully')], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $this->translator->trans('An error occurred while deleting the message')], 500);
        }
    }
}
