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
use App\Contracts\Discussion\DiscussionHandlerInterface;

class DiscussionService implements DiscussionHandlerInterface
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
}