<?php

namespace App\Security\Messaging;

use App\Entity\Message;
use App\Entity\FileMessage;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Controller\Search\SearchMessages;
use App\Entity\User;
use App\Entity\DiscussionMessageUser;
use App\Controller\Pagination\Pagination;
use App\Services\File\FileUploader;
use App\Entity\Discussion;

class MessageService
{
    const LIMIT = 8;

    const DIRECTORY_FILES_MESSAGE = 'files/message';

    public function __construct(
        private EntityManagerInterface $em,
        private FileUploader $fileUploader,
        private Environment $environment,
        private Security $security,
        private Pagination $pagination,
        private SearchMessages $searchMessages,
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

        $user = $this->security->getUser();

        $message = $this->setMessageObject($message, $user);

        $this->filesMessageUploader($user, $messageForm, $message);

        $messageDiscussionUser = $this->setMessageDiscussionUser($message, $discussion, $user);
        
        $this->setDiscussion($discussion, $user);

        return new JsonResponse([
            'code' => Message::MESSAGE_ADDED_SUCCESSFULLY,
            'html' => $this->environment->render('message/new_message.html.twig', [
                'message' => $messageDiscussionUser->getMessage()
            ]),
        ]);
    }

    public function filesMessageUploader(User $user, FormInterface $messageForm, Message $message) : void
    {
        $files = $messageForm->get('files')->getData();

        foreach ($files as $file) {
            if ($file) {
                $fileName = $this->fileUploader->upload($file, self::DIRECTORY_FILES_MESSAGE);

                $fileMessage = new FileMessage();

                $fileMessage->setName($fileName)
                    ->setMessage($message)
                    ->setCreatorUser($user) 
                    ->setDateCreation(new \DateTime())
                    ->setDateModification(new \DateTime());
                
                $this->em->persist($fileMessage);

                $this->em->flush();
            }
        }
    }

    public function messagesPaginationInfos(User $user, Discussion $discussion, int $page) : array
    {
        return $this->pagination->getPagination(
            $this->searchMessages->findMessages($user, $discussion),
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

    private function setDiscussion(Discussion $discussion, User $user) : void
    {
        if ($user == $discussion->getPersonOne()) {
            $discussion->setPersonOneNumberUnreadMessages(
                $discussion->getPersonOneNumberUnreadMessages() + 1
            );
        } else {
            $discussion->setPersonTwoNumberUnreadMessages(
                $discussion->getPersonTwoNumberUnreadMessages() + 1
            );
        }

        $discussion->setModifierUser($user);

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