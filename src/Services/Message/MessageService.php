<?php

namespace App\Services\Message;

use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use App\Entity\Message;
use App\Entity\User;
use App\Form\Type\Message\MessageFormType;
use App\Contracts\Message\MessageHandlerInterface;
use App\MessageRealTime\Message\MessageQueue;
use App\Services\Form\FormErrorExtractor;
use App\Services\Message\AnswerToMessageService;
use App\Services\Message\FilesMessageUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MessageService implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Environment $twig,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly MessageBusInterface $messageBus,
        private readonly FormErrorExtractor $formErrorExtractor,
        private readonly FilesMessageUploaderService $filesMessageUploader,
        private readonly AnswerToMessageService $answerToMessageService
    ) {
    }

    /**
     * Handle the message form submission.
     */
    public function handleMessageFormData(
        FormInterface $messageForm,
        int $idDiscussion
    ): JsonResponse {
        try {
            if (!$messageForm->isValid()) {
                return $this->handleInvalidForm($messageForm);
            }

            $user = $this->security->getUser();

            if (!$user instanceof User) {
                return new JsonResponse([
                    'code' => 'UNAUTHORIZED',
                    'message' => $this->translator->trans(
                        'You must be authenticated'
                    ),
                ], 401);
            }

            $discussion = $this->entityManager
                ->getRepository(Discussion::class)
                ->find($idDiscussion);

            if (!$discussion instanceof Discussion) {
                return new JsonResponse([
                    'code' => 'DISCUSSION_NOT_FOUND',
                    'message' => $this->translator->trans(
                        'Discussion not found'
                    ),
                ], 404);
            }

            return $this->handleValidForm(
                $messageForm,
                $discussion,
                $user
            );
        } catch (\Throwable $exception) {
            dump($exception); // Pour le développement : afficher l'exception dans la console.
            // En production : logger l'exception avant de retourner la réponse.
            return new JsonResponse([
                'code' => 'MESSAGE_ERROR',
                'message' => $this->translator->trans(
                    'An error occurred while processing the message'
                ),
            ], 500);
        }
    }

    /**
     * Handle a valid form submission.
     */
    private function handleValidForm(
        FormInterface $messageForm,
        Discussion $discussion,
        User $user
    ): JsonResponse {
        /** @var Message $message */
        $message = $messageForm->getData();

        $now = new \DateTimeImmutable();

        // Prepare message
        $message
            ->setCreatorUser($user)
            ->setIsRead(false)
            ->setDateCreation($now)
            ->setDateModification($now);

        $this->entityManager->persist($message);

        // Upload message files
        $this->filesMessageUploader->uploader(
            $user,
            $messageForm
        );

        // Create relationship between discussion and message
        $messageDiscussionUser = new DiscussionMessageUser();

        $messageDiscussionUser
            ->setMessage($message)
            ->setDiscussion($discussion)
            ->setCreatorUser($user)
            ->setDateCreation($now)
            ->setDateModification($now);

        $this->entityManager->persist($messageDiscussionUser);

        // Update discussion
        $discussion
            ->setModifierUser($user)
            ->setDateModification($now);

        // Handle answer to another message
        $toAnswerId = $messageForm->get('toAnswer')->getData();

        if ($toAnswerId) {
            $toAnswer = $this->entityManager
                ->getRepository(Message::class)
                ->find($toAnswerId);

            if ($toAnswer instanceof Message) {
                $this->answerToMessageService->answerMessage(
                    $messageDiscussionUser,
                    $toAnswer,
                    $user
                );
            }
        }

        /*
         * One flush only.
         *
         * Doctrine persists:
         * - Message
         * - DiscussionMessageUser
         * - Discussion
         */
        $this->entityManager->flush();

        // Dispatch asynchronous processing
        /*
        if ($message->getMessage()) {
            $this->messageBus->dispatch(
                new MessageQueue($message->getMessage())
            );
        }
        */

        return new JsonResponse([
            'code' => Message::ADDED_SUCCESSFULLY,
            'message' => $this->translator->trans(
                'Element added successfully'
            ),
            'html' => $this->twig->render(
                'message/message.html.twig',
                [
                    'item' => $messageDiscussionUser,
                ]
            ),
        ]);
    }

    /**
     * Handle an invalid form submission.
     */
    private function handleInvalidForm(
        FormInterface $messageForm
    ): JsonResponse {
        return new JsonResponse([
            'code' => Message::MESSAGE_INVALID_FORM,
            'errors' => $this->formErrorExtractor->extractErrors(
                $messageForm
            ),
        ]);
    }

    /**
     * Mark all unread messages as read.
     */
    public function markUnreadMessagesAsRead(
        int $idDiscussion,
        User $user
    ): void {
        $discussion = $this->entityManager
            ->getRepository(Discussion::class)
            ->find($idDiscussion);

        if (!$discussion instanceof Discussion) {
            return;
        }

        $unreadMessages = $this->unreadMessages(
            $discussion,
            $user
        );

        foreach ($unreadMessages as $message) {
            if ($message instanceof Message) {
                $message->setIsRead(true);
            }
        }

        // One flush instead of one flush per message.
        $this->entityManager->flush();
    }

    /**
     * Retrieve unread messages for a discussion and user.
     *
     * @return Message[]
     */
    public function unreadMessages(
        Discussion $discussion,
        User $user
    ): array {
        return $this->entityManager
            ->getRepository(Message::class)
            ->getUnreadMessages(
                $discussion,
                $user
            );
    }
}