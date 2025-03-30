<?php

namespace App\Controller\Messaging\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\MessageFormType;
use App\Services\Message\MessageService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message;
use App\Entity\FileMessage;
use App\Entity\SearchMessage;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use Elastica\Query\Range;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Entity\AnswerMessage;
use Twig\Environment;
use App\Services\Mercure\MercureClient;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\User\UserService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\Message\MessageSearchService;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageService $messageService,
        private readonly Environment $environment,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly UserService $userService,
        private readonly MessageSearchService $messageSearchService
    ) {}

    #[Route('/user/message/{idDiscussion}/{page}', name: 'app_message', options: ['expose' => true])]
    public function index(Request $request, int $idDiscussion = 0, int $page = 1): JsonResponse
    {
        try {
            $user = $this->userService->getAuthenticatedUser();
            $message = new Message();
            $idDiscussion = $request->get('idDiscussion'); 
            $page = $request->get('page'); 
            $criteria = $request->get('criteria');

            $searchMessage = $this->messageSearchService->saveSearch($user, $criteria);
            $discussion = $this->em->getRepository(Discussion::class)->find($idDiscussion); 

            $this->messageService->setDiscussioReadingMessageStatus($discussion, $user);

            $messagesPaginationInfos = $this->messageSearchService->messagesPaginationInfos(
                $user, 
                $discussion, 
                $page, 
                $searchMessage['searchMessage'], 
                $searchMessage['saveSearch']
            );

            $messageForm = $this->createForm(MessageFormType::class, $message);
            $messageForm->handleRequest($request);

            if ($messageForm->isSubmitted() && $messageForm->isValid()) {
                return $this->messageService->handleMessageFormData($messageForm, $discussion);
            }

            return new JsonResponse([
                'html' => $this->environment->render('message/form_message.html.twig', [
                    'formMessage' => $messageForm->createView(),
                ]),
                'messages' => $this->environment->render('message/list.html.twig', [
                    'discussion' => $discussion,
                    'page' => $page,
                    'numbrePagesPagination' => $messagesPaginationInfos['numbrePagesPagination'],
                    'messages' => $messagesPaginationInfos['data'],
                ]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $this->translator->trans('An error occurred')], 500);
        }
    }

    #[Route('/user/delete/message/{id}', name: 'app_delete_message', methods: ['DELETE'], options: ['expose' => true])]
    public function delete(int $id): JsonResponse
    {
        try {
            $discussionMessageUser = $this->em->getRepository(DiscussionMessageUser::class)->find($id);

            if (!$discussionMessageUser) {
                return new JsonResponse(['message' => $this->translator->trans('Message not found')], 404);
            }

            $message = $discussionMessageUser->getMessage();
            $fileMessages = $message->getFileMessages();
            $answerMessages = $discussionMessageUser->getAnswerMessages();

            // Suppression des fichiers associés
            foreach ($fileMessages as $file) {
                $this->em->remove($file);
            }

            // Suppression des réponses associées
            foreach ($answerMessages as $answer) {
                $this->em->remove($answer);
            }

            // Suppression du message et de la relation
            $this->em->remove($message);
            $this->em->remove($discussionMessageUser);
            $this->em->flush();

            return new JsonResponse(['message' => $this->translator->trans('Element deleted successfully')], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $this->translator->trans('An error occurred while deleting the message')], 500);
        }
    }
}