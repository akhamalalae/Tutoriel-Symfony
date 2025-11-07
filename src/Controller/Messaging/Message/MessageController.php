<?php

namespace App\Controller\Messaging\Message;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\FileMessage;
use App\Entity\SearchMessage;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\MessageFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Services\User\UserService;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Contracts\Message\MessageRendererInterface;
use App\Contracts\Message\MessageDeleterInterface;
use App\Contracts\Message\MessageHandlerInterface;
use App\Contracts\Message\SaveMessageSearchInterface;
use Symfony\Component\Form\FormInterface;
use App\Contracts\Error\ErrorResponseInterface;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserService $userService,
        private readonly MessageRendererInterface $renderer,
        private readonly MessageDeleterInterface $deleter,
        private readonly MessageHandlerInterface $handler,
        private readonly ErrorResponseInterface $errorResponseService,
        private readonly SaveMessageSearchInterface $saveMessageSearchService,
    ) {}

    /**
     * Display and handle the message form
     * 
     * @param Request $request The HTTP request
     * @param int $idDiscussion The discussion ID
     * @param int $page The current page number
     * 
     * @return JsonResponse The response containing the rendered form and messages
     * 
     * @throws \Exception When an error occurs during processing
     */
    #[Route('/user/message/{idDiscussion}/{page}', name: 'app_message', options: ['expose' => true])]
    public function index(Request $request, int $idDiscussion = 0, int $page = 1): JsonResponse
    {
        try {
            $user = $this->userService->getAuthenticatedUser();

            // Récupération des paramètres
            $criteria = $request->get('criteria', []);
            $idDiscussion = $request->get('idDiscussion', $idDiscussion);
            $page = $request->get('page', $page);

            // Création et traitement du formulaire
            $messageForm = $this->createMessageForm($request);

            if ($messageForm['submitted'] && $messageForm['valid']) {
                return $this->handler->handleMessageFormData($messageForm['form'], $idDiscussion);
            }

            // Enregistrement de la recherche
            $searchMessage = $this->saveSearch($user, $criteria);

            // Marquer les messages non lus comme lus
            $this->markMessagesAsRead($idDiscussion, $user);

            // Retour JSON
            return $this->renderJsonResponse($messageForm['form'], $idDiscussion, $page, $searchMessage);

        } catch (\Exception $e) {
            return $this->errorResponseService->createErrorResponse($e);
        }
    }

    /**
     * Crée et traite le formulaire de message
     * 
     * @param Request $request The HTTP request
     * 
     * @return array The form, submission status, and validity
     * 
     * @throws \Exception When form creation or handling fails
     */
    private function createMessageForm(Request $request): array
    {
        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        return [
            'form' => $form,
            'submitted' => $form->isSubmitted(),
            'valid' => $form->isSubmitted() && $form->isValid()
        ];
    }

    /**
     * Enregistre la recherche de messages
     * 
     * @param User $user The authenticated user
     * @param array $criteria The search criteria
     * 
     * @return SearchMessage|null The saved SearchMessage entity or null
     */
    private function saveSearch(User $user, array $criteria): ?SearchMessage
    {
        return !empty($criteria)
            ? $this->saveMessageSearchService->saveSearch($user, $criteria)
            : null;
    }

    /**
     * Marque les messages non lus comme lus
     */
    private function markMessagesAsRead(int $idDiscussion, User $user): void
    {
        $this->handler->markUnreadMessagesAsRead($idDiscussion, $user);
    }

    /**
     * Renders the JSON response with form and messages
     * 
     * @param FormInterface $form The message form
     * @param int $idDiscussion The discussion ID
     * @param int $page The current page number
     * @param SearchMessage|null $searchMessage The search message criteria
     * 
     * @return JsonResponse The JSON response
     */
    private function renderJsonResponse(FormInterface $form, int $idDiscussion, int $page, ?SearchMessage $searchMessage): JsonResponse
    {
        return new JsonResponse([
            'html' => $this->renderer->renderForm($form),
            'messages' => $this->renderer->renderMessages($idDiscussion, $page, $searchMessage),
            'searchMessage' => $searchMessage ? $searchMessage->getId() : null,
        ]);
    }


    /**
     * Delete a message by ID
     * 
     * @param int $id The message ID
     * 
     * @return JsonResponse The response indicating success or failure
     * 
     * @throws \Exception When an error occurs during deletion
     */
    #[Route('/user/delete/message/{id}', name: 'app_delete_message', methods: ['DELETE'], options: ['expose' => true])]
    public function delete(int $id): JsonResponse
    {
        return $this->deleter->deleteMessage($id);
    }

    /**
     * Delete a saved search message by ID
     * 
     * @param int $id The search message ID
     * 
     * @return JsonResponse The response indicating success or failure
     * 
     * @throws \Exception When an error occurs during deletion
     */
    #[Route('/user/delete/search/message/{id}', name: 'app_delete_search_message', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteSearchMessage(int $id): JsonResponse
    {
        return $this->deleter->deleteSearchMessage($id);
    }
}