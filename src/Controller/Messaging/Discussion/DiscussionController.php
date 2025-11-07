<?php

namespace App\Controller\Messaging\Discussion;

use App\Entity\Message;
use App\Entity\Discussion;
use App\Entity\SearchDiscussion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Discussion\DiscussionFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\Breadcrumb\BreadcrumbService;
use App\Services\User\UserService;
use App\Contracts\Discussion\DiscussionHandlerInterface;
use App\Contracts\Discussion\DiscussionRendererInterface;
use App\Contracts\Discussion\SaveDiscussionSearchInterface;
use App\Contracts\Discussion\DiscussionDeleterInterface;
use App\Contracts\Error\ErrorResponseInterface;

#[IsGranted('ROLE_USER')]
class DiscussionController extends AbstractController
{
    public function __construct(
        private readonly BreadcrumbService $breadcrumbService,
        private readonly UserService $userService,
        private readonly DiscussionHandlerInterface $discussionHandler,
        private readonly DiscussionRendererInterface $renderer,
        private readonly SaveDiscussionSearchInterface $saveDiscussionSearchService,
        private readonly DiscussionDeleterInterface $discussionDeleter,
        private readonly ErrorResponseInterface $errorResponseService,
    ) {}

    #[Route('/user/discussion', name: 'app_discussion')]
    public function index(Request $request): Response
    {      
        $this->breadcrumbService->addBreadcrumb('Discussion', $this->generateUrl('app_discussion'));

        $discussion = new Discussion();
        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);
        $discussionForm->handleRequest($request);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $this->discussionHandler->handleDiscussionFormData($discussionForm);
        }

        return $this->renderer->renderDiscussionForm($discussionForm);
    }

    #[Route('/user/list/discussion/{page}', name: 'app_list_discussion', options: ['expose' => true])]
    public function list(Request $request, int $page = 1) : Response
    {    
        try {
            $user = $this->userService->getAuthenticatedUser();

            $page = $request->get('page'); 

            $criteria = $request->get('criteria');

            $searchDiscussion = $this->saveDiscussionSearchService
                ->saveSearch($criteria);

            return new JsonResponse([
                'discussions' => $this->renderer->renderListDiscussion($page, $searchDiscussion),
                'searchDiscussion' => $searchDiscussion ? $searchDiscussion->getId() : null,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponseService->createErrorResponse($e);
        }    
    }

    #[Route('/user/delete/discussion/{id}', name: 'app_delete_discussion', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteDiscussion(int $id): JsonResponse
    {
        $this->discussionDeleter->deleteDiscussion($id);
    }

    #[Route('/user/delete/search/discussion/{id}', name: 'app_delete_search_discussion', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteSearchDiscussion(int $id): JsonResponse
    {
        $this->discussionDeleter->deleteSearchDiscussion($id);
    }
}
