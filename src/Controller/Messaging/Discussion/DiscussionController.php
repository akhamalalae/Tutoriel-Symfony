<?php

namespace App\Controller\Messaging\Discussion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Discussion\DiscussionFormType;
use App\Services\Discussion\DiscussionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\SearchDiscussion;
use App\Entity\Discussion;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use App\Controller\Pagination\Pagination;
use App\Services\Breadcrumb\BreadcrumbService;
class DiscussionController extends AbstractController
{
    #[Route('/user/discussion', name: 'app_discussion')]
    public function index(
        Request $request,
        RequestStack $requestStack, 
        DiscussionService $discussionService, 
        BreadcrumbService $breadcrumbService
    ) : Response
    {      
        $breadcrumbService->addBreadcrumb('Discussion', $this->generateUrl('app_discussion'));

        $request = $requestStack->getMainRequest();

        $discussion = new Discussion();

        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);

        $discussionForm->handleRequest($request);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $discussionService->handleDiscussionFormData($discussionForm);
        }

        return $this->render('discussion/index.html.twig', [
            'formDiscussion' => $discussionForm->createView()
        ]);
    }

    #[Route('/user/list/discussion/{page}', name: 'app_list_discussion', options: ['expose' => true])]
    public function listDiscussions(
        Request $request,
        EntityManagerInterface $em,
        Environment $environment,
        DiscussionService $discussionService, 
        Security $security,
        Pagination $pagination,
        int $page = 1,
    ) : Response
    {        
        $user = $security->getUser();

        $page = $request->get('page'); 

        $criteria = $request->get('criteria');

        $searchDiscussion = $this->saveSearch($user, $criteria, $em);

        $discussionPaginationInfos = $discussionService->searchDiscussions($page, $searchDiscussion['searchDiscussion'], $searchDiscussion['saveSearch']);

        return new JsonResponse([
            'discussions' => $environment->render('discussion/list.html.twig', [
                'page' => $page,
                'numbrePagesPagination' => $discussionPaginationInfos['numbrePagesPagination'],
                'discussions' => $discussionPaginationInfos['data'],
            ]),
        ]);
    }

    #[Route('/user/search/discussion', name: 'app_search_discussion', options: ['expose' => true])]
    public function searchDiscussion(
        Request $request,
        EntityManagerInterface $em,
        Environment $environment,
        Security $security,
    ) : Response
    {        
        $user = $security->getUser();

        $idSearchDiscussion = $request->get('idSearchDiscussion');
        
        $selectedSearchDiscussion = $idSearchDiscussion ? $em->getRepository(SearchDiscussion::class)->find($idSearchDiscussion) : null;

        $searchDiscussion = $em->getRepository(SearchDiscussion::class)->findBy(['creatorUser' => $user]);

        return new JsonResponse([
            'html' => $environment->render('discussion/search_discussion_with_criteria.html.twig', [
                'searchDiscussion' => $searchDiscussion,
                'selectedSearchDiscussion' => $selectedSearchDiscussion
            ]),
        ]);
    }

    private function saveSearch(
        User $user,
        array|null $criteria,
        EntityManagerInterface $em) : array
    {
        $searchDiscussion = null;

        $saveSearch = false;

        if ($criteria) {
            $saveSearch = $criteria['saveSearch'];

            $searchDiscussion = new SearchDiscussion();

            $name = $criteria['name'];

            $firstName = $criteria['firstName'];

            $createdThisMonth = $criteria['createdThisMonth'] == 'true' ? true : false;

            $description = $criteria['description'];

            $searchDiscussion->setCreatorUser($user)
                ->setCreatedThisMonth($createdThisMonth)
                ->setDateCreation(new \DateTime())
                ->setName($name)
                ->setFirstName($firstName)
                ->setDescription($description)
            ;

            $em->persist($searchDiscussion);

            $em->flush();
        }

        return [
            'searchDiscussion' => $searchDiscussion,
            'saveSearch' => $saveSearch == 'true' ? true : false
        ];
    }
}
