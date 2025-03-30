<?php

namespace App\Services\Message;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\AnswerMessage;
use App\Entity\DiscussionMessageUser;
use Doctrine\ORM\EntityManagerInterface;

class AnswerToMessageService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Set up an answer to a message
     *
     * @param DiscussionMessageUser $messageDiscussionUser The message discussion user
     * @param Message $toAnswer The message to answer
     * @param User $user The user answering
     */
    public function answerMessage(DiscussionMessageUser $messageDiscussionUser, Message $toAnswer, User $user): void
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
}