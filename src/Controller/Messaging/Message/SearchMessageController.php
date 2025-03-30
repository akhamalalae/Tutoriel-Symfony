<?php

namespace App\Controller\Messaging\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SearchMessage;
use Twig\Environment;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\User\UserService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SearchMessageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Environment $environment,
        private readonly TranslatorInterface $translator,
        private readonly UserService $userService
    ) {}

    /**
     * Search for messages with criteria
     *
     * @param Request $request The request containing search parameters
     * @return Response JSON response with rendered template
     */
    #[Route('/user/search/message', name: 'app_search_message', options: ['expose' => true])]
    public function searchMessage(Request $request): Response
    {        
        try {
            $user = $this->userService->getAuthenticatedUser();
            $idDiscussion = $request->get('idDiscussion'); 
            $page = $request->get('page'); 
            $idSearchMessage = $request->get('idSearchMessage');

            $selectedSearchMessage = $idSearchMessage ? $this->em->getRepository(SearchMessage::class)->find($idSearchMessage) : null;
            $searchMessage = $this->em->getRepository(SearchMessage::class)->findBy(['creatorUser' => $user]);

            return new JsonResponse([
                'html' => $this->environment->render('message/search_message_with_criteria.html.twig', [
                    'searchMessage' => $searchMessage,
                    'selectedSearchMessage' => $selectedSearchMessage,
                    'idDiscussion' => $idDiscussion,
                    'page' => $page
                ]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $this->translator->trans('An error occurred while searching messages')
            ], 500);
        }
    }
}