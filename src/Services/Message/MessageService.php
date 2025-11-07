<?php

namespace App\Services\Message;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Message;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Form\FormErrorExtractor;
use Symfony\Component\Form\FormInterface;
use App\MessageRealTime\Message\MessageQueue;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Services\Message\FilesMessageUploaderService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\AnswerMessage;
use App\Entity\SearchMessage;
use App\Services\Message\AnswerToMessageService;
use App\Form\Type\Message\MessageFormType;
use App\Contracts\Message\MessageHandlerInterface;

class MessageService implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Environment $environment,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly MessageBusInterface $bus,
        private readonly FormErrorExtractor $getErrorForm,
        private readonly FilesMessageUploaderService $filesMessageUploader,
        private readonly AnswerToMessageService $answerToMessageService
    ) {}

    /**
     * Handle the message form submission
     *
     * @param FormInterface $messageForm The submitted form
     * @param int $discussion The discussion to add the message to
     * @return JsonResponse The response containing the result
     * @throws \Exception When message processing fails
     */
    public function handleMessageFormData(FormInterface $messageForm, int $idDiscussion): JsonResponse
    {
        try {
            if ($messageForm->isValid()) {
                $discussion = $this->em->getRepository(Discussion::class)->find($idDiscussion); 
                return $this->handleValidForm($messageForm, $discussion);
            }
            return $this->handleInvalidForm($messageForm);
        } catch (\Exception $e) {

            return new JsonResponse([
                'code' => Message::ERROR,
                'message' => $this->translator->trans('An error occurred while processing the message')
            ], 500);
        }
    }

    /**
     * Handle a valid form submission
     *
     * @param FormInterface $messageForm The valid form
     * @param Discussion $discussion The discussion
     * @return JsonResponse The success response
     */
    private function handleValidForm(FormInterface $messageForm, Discussion $discussion): JsonResponse
    {
        /** @var Message $message */
        $message = $messageForm->getData();
        $toAnswerId = $messageForm->get('toAnswer')->getData();
        $user = $this->security->getUser();

        $message = $this->setMessageObject($message, $user);
        $this->filesMessageUploader->uploader($user, $messageForm);

        $messageDiscussionUser = $this->setMessageDiscussionUser($message, $discussion, $user);
        $this->setDiscussion($discussion, $user);

        if ($toAnswerId) {
            $toAnswer = $this->em->getRepository(Message::class)->find($toAnswerId);
            if ($toAnswer) {
                $this->answerToMessageService->answerMessage($messageDiscussionUser, $toAnswer, $user);
            }
        }

        if ($message->getMessage()) {
            $this->bus->dispatch(new MessageQueue($message->getMessage()));
        }

        return new JsonResponse([
            'code' => Message::ADDED_SUCCESSFULLY,
            'message' => $this->translator->trans('Element added successfully'),
            'html' => $this->environment->render('message/message.html.twig', [
                'item' => $messageDiscussionUser
            ]),
        ]);
    }

    /**
     * Handle an invalid form submission
     *
     * @param FormInterface $messageForm The invalid form
     * @return JsonResponse The error response
     */
    private function handleInvalidForm(FormInterface $messageForm): JsonResponse
    {
        return new JsonResponse([
            'code' => Message::MESSAGE_INVALID_FORM,
            'errors' => $this->getErrorForm->extractErrors($messageForm)
        ]);
    }

    /**
     * Set up the message object with basic information
     *
     * @param Message $message The message to set up
     * @param User $user The user creating the message
     * 
     * @return Message The configured message
     */
    private function setMessageObject(Message $message, User $user): Message
    {
        $message->setCreatorUser($user)
            ->setIsRead(false)
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime());
        
        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    /**
     * Create and set up the message discussion user relationship
     *
     * @param Message $message The message
     * @param Discussion $discussion The discussion
     * @param User $user The user
     * 
     * @return DiscussionMessageUser The created relationship
     */
    private function setMessageDiscussionUser(Message $message, Discussion $discussion, User $user): DiscussionMessageUser
    {
        $messageDiscussionUser = new DiscussionMessageUser();
        $messageDiscussionUser->setMessage($message)
            ->setDiscussion($discussion)
            ->setCreatorUser($user)
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime());
        
        $this->em->persist($messageDiscussionUser);
        $this->em->flush();

        return $messageDiscussionUser;
    }

    /**
     * Update the discussion with new message information
     *
     * @param Discussion $discussion The discussion to update
     * @param User $user The user making the update
     * 
     * @return void
     */
    private function setDiscussion(Discussion $discussion, User $user): void
    {
        $discussion->setModifierUser($user)
            ->setDateModification(new \DateTime());

        $this->em->persist($discussion);
        $this->em->flush();
    }

    /**
     * Mark all unread messages as read
     *
     * @param int $discussion The discussion containing the messages
     * @param User $user The user marking the messages as read
     * 
     * @return void
     */
    public function markUnreadMessagesAsRead(int $idDiscussion, User $user): void
    {
        $discussion = $this->em
            ->getRepository(Discussion::class)->find($idDiscussion); 

        $unreadMessages = $this->unreadMessages($discussion, $user);

        foreach ($unreadMessages as $message) {
            if ($message instanceof SearchMessage) {
                $message->setIsRead(true);
                
                $this->em->persist($message);
                $this->em->flush();
            }
        }
    }

    public function unreadMessages(Discussion $discussion, User $user): array
    {
        return $this->em->getRepository(Message::class)
                ->getUnreadMessages($discussion, $user);
    }
}