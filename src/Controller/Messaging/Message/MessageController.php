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
class MessageController extends AbstractController
{
    #[Route('/user/message/{idDiscussion}/{page}', name: 'app_message', options: ['expose' => true])]
    public function index(
        Request $request,
        RequestStack $requestStack, 
        MessageService $messageService, 
        Environment $environment,
        EntityManagerInterface $em,
        Security $security,
        int $idDiscussion = 0,
        int $page = 1) : JsonResponse
    {
        $user = $security->getUser();

        $message = new Message();

        $idDiscussion = $request->get('idDiscussion'); 

        $page = $request->get('page'); 

        $criteria = $request->get('criteria');

        $searchMessage = $this->saveSearch($user, $criteria, $em);

        $discussion = $em->getRepository(Discussion::class)->find($idDiscussion); 

        $this->setDiscussioReadingMessageStatus($em, $discussion, $user);

        $messagesPaginationInfos = $messageService->messagesPaginationInfos($user, $discussion, $page, $searchMessage['searchMessage'], $searchMessage['saveSearch']);

        $request = $requestStack->getMainRequest();

        $messageForm = $this->createForm(MessageFormType::class, $message);

        $messageForm->handleRequest($request);

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            return $messageService->handleMessageFormData($messageForm, $discussion);
        }

        return new JsonResponse([
            'html' => $environment->render('message/form_message.html.twig', [
                'formMessage' => $messageForm->createView(),
            ]),
            'messages' => $environment->render('message/list.html.twig', [
                'discussion' => $discussion,
                'page' => $page,
                'numbrePagesPagination' => $messagesPaginationInfos['numbrePagesPagination'],
                'messages' => $messagesPaginationInfos['data'],
            ]),
        ]);
    }

    #[Route('/user/search/message', name: 'app_search_message', options: ['expose' => true])]
    public function searchMessage(
        Request $request,
        EntityManagerInterface $em,
        Environment $environment,
        Security $security,
    ) : Response
    {        
        $user = $security->getUser();

        $idDiscussion = $request->get('idDiscussion'); 

        $page = $request->get('page'); 

        $idSearchMessage = $request->get('idSearchMessage');

        $selectedSearchMessage = $idSearchMessage ? $em->getRepository(SearchMessage::class)->find($idSearchMessage) : null;

        $searchMessage = $em->getRepository(SearchMessage::class)->findBy(['creatorUser' => $user]);

        return new JsonResponse([
            'html' => $environment->render('message/search_message_with_criteria.html.twig', [
                'searchMessage' => $searchMessage,
                'selectedSearchMessage' => $selectedSearchMessage,
                'idDiscussion' => $idDiscussion,
                'page' => $page
            ]),
        ]);
    }

    private function saveSearch(
        User $user,
        array|null $criteria,
        EntityManagerInterface $em) : array
    {
        $searchMessage = null;

        $saveSearch = false;

        if ($criteria) {
            $saveSearch = $criteria['saveSearch'];

            $searchMessage = new SearchMessage();

            $message = $criteria['message'];

            $fileName = $criteria['fileName'];

            $createdThisMonth = $criteria['createdThisMonth'] == 'true' ? true : false;

            $description = $criteria['description'];

            $searchMessage->setCreatorUser($user)
                ->setCreatedThisMonth($createdThisMonth)
                ->setDateCreation(new \DateTime())
                ->setFileName($fileName)
                ->setMessage($message)
                ->setDescription($description)
            ;

            $em->persist($searchMessage);

            $em->flush();
        }

        return [
            'searchMessage' => $searchMessage,
            'saveSearch' => $saveSearch == 'true' ? true : false
        ];
    }

    private function setDiscussioReadingMessageStatus(
        EntityManagerInterface $em, 
        Discussion $discussion, 
        User $user) : void
    {
        if ($user == $discussion->getPersonInvitationSender()) {
            $discussion->setPersonInvitationRecipientNumberUnreadMessages(null);
        } else {
            $discussion->setPersonInvitationSenderNumberUnreadMessages(null);
        }

        $discussion->setModifierUser($user)
            ->setDateModification(new \DateTime());

        $em->persist($discussion);

        $em->flush();
    }

    #[Route('/user/delete/message/{id}', name: 'app_delete_message', methods: ['DELETE'], options: ['expose' => true])]
    public function delete(
        int $id,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): JsonResponse
    {
        $discussionMessageUser = $em->getRepository(DiscussionMessageUser::class)->find($id);

        $message = $discussionMessageUser->getMessage();

        $fileMessages = $message->getFileMessages();

        $answerMessages = $discussionMessageUser->getAnswerMessages();

        if (!$discussionMessageUser) {
            return new JsonResponse(['message' => 'Message not found'], 404);
        }

        foreach ($fileMessages as $file) {
            $em->remove($file);
        }

        $em->remove($message);

        foreach ($answerMessages as $answer) {
            $em->remove($answer);
        }

        $em->remove($discussionMessageUser);

        $em->flush();

        return new JsonResponse(['message' => $translator->trans('Element deleted successfully')], 200);
    }
}