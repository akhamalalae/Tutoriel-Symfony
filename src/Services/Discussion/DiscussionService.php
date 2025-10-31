<?php

namespace App\Services\Discussion;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\Form\FormErrorExtractor;
use App\Services\User\UserService;

class DiscussionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Environment $environment,
        private TranslatorInterface $translator,
        private FormErrorExtractor $getErrorForm,
        private UserService $userService,
    ) {}

    public function handleDiscussionFormData(FormInterface $discussionForm): JsonResponse
    {
        return $discussionForm->isValid()
            ? $this->handleValidForm($discussionForm)
            : $this->handleInvalidForm($discussionForm);
    }

    private function handleValidForm(FormInterface $discussionForm) : JsonResponse
    {
        /** @var Discussion $discussion */
        $discussion = $discussionForm->getData();

        $user = $this->userService->getAuthenticatedUser();

        $discussion->setPersonInvitationSender($user)
            ->setPersonInvitationRecipient($discussion->getPersonInvitationRecipient())
            ->setCreatorUser($user)
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime());

        $this->em->persist($discussion);

        $this->em->flush();

        return new JsonResponse([
            'code' => Discussion::ADDED_SUCCESSFULLY,
            'message' => $this->translator->trans('Element added successfully'),
            'html' => $this->environment->render('discussion/discussion.html.twig', [
                'item' => $discussion
            ])
        ]);
    }

    private function handleInvalidForm(FormInterface $discussionForm) : JsonResponse
    {
        try {
            $errors = $this->getErrorForm->extractErrors($discussionForm);
    
            return new JsonResponse([
                'code' => Discussion::DISCUSSION_INVALID_FORM,
                'errors' => $errors
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => 'ERROR_PROCESSING_FORM',
                'message' => 'An error occurred while processing the form.',
                'details' => $e->getMessage() // Facultatif, à éviter en production
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * get unread messages for each discussion for the current user.
     *
     * @param array $discussions
     * @param User $user
     * @return array DiscussionUnreadMessages
     */
    public function getDiscussionUnreadMessages(array $discussions, User $user): array
    {
        $totalUnreadMessages = 0;
        $discussionUnreadMessages = [];

        if (empty($discussions) || !$user) {
            return [
                'discussionUnreadMessages' => [],
                'numberTotalUnreadMessages' => 0,
            ];
        }

        foreach ($discussions as $discussion) {
            // Sécurité : on ignore si ce n’est pas une instance valide
            if (!$discussion instanceof Discussion) {
                continue;
            }

            // Récupération des messages non lus
            $unreadMessages = $this->em
                ->getRepository(Message::class)
                ->getUnreadMessages($discussion, $user);

            // Comptage global
            $totalUnreadMessages += \count($unreadMessages);

            // Ajout des données par discussion
            $discussionUnreadMessages[] = [
                'discussionId' => $discussion->getId(),
                'unreadMessages' => $unreadMessages,
            ];
        }

        // Retour propre et structuré
        return [
            'discussionUnreadMessages' => $discussionUnreadMessages,
            'numberTotalUnreadMessages' => $totalUnreadMessages,
        ];
    }

}