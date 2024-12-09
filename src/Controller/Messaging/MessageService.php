<?php

namespace App\Controller\Messaging;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Controller\Search\SearchMessages;
use App\Entity\User;
use App\Entity\DiscussionMessageUser;
use App\Entity\Discussion;
use Symfony\Component\HttpFoundation\Request;

class MessageService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Environment $environment,
        private Security $security,
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
        $message = $this->setMessage($message, $user);
        $messageDiscussionUser = $this->setMessageDiscussionUser($message, $discussion, $user);

        return new JsonResponse([
            'code' => Message::MESSAGE_ADDED_SUCCESSFULLY,
            'html' => $this->environment->render('message/new-message.html.twig', [
                'message' => $messageDiscussionUser->getMessage()
            ]),
        ]);
    }

    public function getMessages(User $user, Discussion $discussion)
    {
        return $this->searchMessages->findMessages($user, $discussion);
    }

    private function setMessage(Message $message, User $user) : Message
    {
        $message->setCreatorUser($user)
            ->setTitle($message->getObjet())
            ->setDescription($message->getObjet())
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