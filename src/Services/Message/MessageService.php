<?php

namespace App\Services\Message;

use App\Entity\Message;
use App\Entity\FileMessage;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Controller\Messaging\Search\Message\SearchMessages;
use App\Entity\User;
use App\Entity\SearchMessage;
use App\Entity\DiscussionMessageUser;
use App\Controller\Pagination\Pagination;
use App\Services\File\FileUploader;
use App\Entity\Discussion;
use App\Entity\AnswerMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use App\MessageRealTime\Message\MessageQueue;
use Symfony\Contracts\Translation\TranslatorInterface;
class MessageService
{
    const LIMIT = 5;

    const DIRECTORY_FILES_MESSAGE = 'files/message';

    public function __construct(
        private EntityManagerInterface $em,
        private FileUploader $fileUploader,
        private Environment $environment,
        private Security $security,
        private Pagination $pagination,
        private SearchMessages $searchMessages,
        private TranslatorInterface $translator,
        private MessageBusInterface $bus
    ) {}

    public function handleMessageFormData(FormInterface $messageForm, Discussion $discussion) : JsonResponse
    {
        if ($messageForm->isValid()) {
            return $this->handleValidForm($messageForm, $discussion);
        } else {
            return $this->handleInvalidForm($messageForm);
        }
    }

    private function handleValidForm(FormInterface $messageForm, Discussion $discussion) : JsonResponse
    {
        /** @var Message $message */
        $message = $messageForm->getData();

        $toAnswerId = $messageForm->get('toAnswer')->getData();

        $user = $this->security->getUser();

        $message = $this->setMessageObject($message, $user);

        $this->filesMessageUploader($user, $messageForm, $message);

        $messageDiscussionUser = $this->setMessageDiscussionUser($message, $discussion, $user);
        
        $this->setDiscussion($discussion, $user);

        if ($toAnswerId) {
            $toAnswer = $this->em->getRepository(Message::class)->find($toAnswerId);
            $this->toAnswerMessage($messageDiscussionUser, $toAnswer, $user);
        }

        if ($message) {
            dump('bus');
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

    public function filesMessageUploader(User $user, FormInterface $messageForm, Message $message) : void
    {
        $files = $messageForm->get('files')->getData();

        foreach ($files as $file) {
            if ($file) {
                $fileUploader = $this->fileUploader->upload($file, self::DIRECTORY_FILES_MESSAGE);

                $fileName = $fileUploader['name'];

                $FileOriginalName = $fileUploader['originalName'];

                $fileMimeType = $fileUploader['mimeType'];

                $fileMessage = new FileMessage();

                $fileMessage->setName($fileName)
                    ->setMimeType($fileMimeType)
                    ->setOriginalName($FileOriginalName)
                    ->setMessage($message)
                    ->setCreatorUser($user) 
                    ->setDateCreation(new \DateTime())
                    ->setDateModification(new \DateTime());
                
                $this->em->persist($fileMessage);

                $message->addFileMessage($fileMessage);

                $this->em->persist($message);

                $this->em->flush();
            }
        }
    }

    public function messagesPaginationInfos(User $user, Discussion $discussion, int $page, SearchMessage|null $criteria, bool $saveSearch) : array
    {
        //$limit = $discussion->getPersonInvitationRecipientNumberUnreadMessages() + $discussion->getPersonInvitationSenderNumberUnreadMessages();

        return $this->pagination->getPagination(
            $this->searchMessages->findMessages($user, $discussion, $criteria, $saveSearch),
            $page,
            self::LIMIT
        );
    }

    private function setMessageObject(Message $message, User $user) : Message
    {
        $message->setCreatorUser($user)
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime());
        
        $this->em->persist($message);

        $this->em->flush();

        return $message;
    }

    private function setMessageDiscussionUser(Message $message, Discussion $discussion, User $user) : DiscussionMessageUser
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

    private function toAnswerMessage(DiscussionMessageUser $messageDiscussionUser, Message $toAnswer, User $user) : void
    {
        $answerMessage = new AnswerMessage();

        $answerMessage->setDiscussionMessageUser($messageDiscussionUser)
            ->setCreatorUser($user)
            ->setDateCreation(new \DateTime())
            ->setMessage($toAnswer);

        $this->em->persist($answerMessage);

        $messageDiscussionUser->addAnswerMessage($answerMessage);

        $this->em->persist($messageDiscussionUser);

        $this->em->flush();
    }

    private function setDiscussion(Discussion $discussion, User $user) : void
    {
        if ($user == $discussion->getPersonInvitationSender()) {
            $discussion->setPersonInvitationSenderNumberUnreadMessages(
                $discussion->getPersonInvitationSenderNumberUnreadMessages() + 1
            );
        } else {
            $discussion->setPersonInvitationRecipientNumberUnreadMessages(
                $discussion->getPersonInvitationRecipientNumberUnreadMessages() + 1
            );
        }

        $discussion->setModifierUser($user)
            ->setDateModification(new \DateTime());

        $this->em->persist($discussion);
        $this->em->flush();
    }

    private function handleInvalidForm(FormInterface $messageForm) : JsonResponse
    {
        return new JsonResponse([
            'code' => Message::MESSAGE_INVALID_FORM,
            'errors' => $this->getErrorMessages($messageForm)
        ]);
    }

    private function getErrorMessages(FormInterface $messageForm): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}