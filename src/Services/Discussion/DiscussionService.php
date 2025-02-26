<?php

namespace App\Services\Discussion;

use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Messaging\Search\Discussion\SearchDiscussions;
use App\Controller\Pagination\Pagination;
use Symfony\Contracts\Translation\TranslatorInterface;
class DiscussionService
{
    const LIMIT = 2;

    public function __construct(
        private EntityManagerInterface $em,
        private Environment $environment,
        private Security $security,
        private SearchDiscussions $searchDiscussions,
        private ParameterBagInterface $parameters,
        private Pagination $pagination,
        private TranslatorInterface $translator
    ) {}

    public function handleDiscussionFormData(FormInterface $discussionForm): JsonResponse
    {
        if ($discussionForm->isValid()) {
            return $this->handleValidForm($discussionForm);
        } else {
            return $this->handleInvalidForm($discussionForm);
        }
    }

    private function handleValidForm(FormInterface $discussionForm) : JsonResponse
    {
        /** @var Discussion $discussion */
        $discussion = $discussionForm->getData();

        $user = $this->security->getUser();

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

    public function searchDiscussions(int $page, SearchDiscussion|null $criteria, bool $saveSearch): array
    {
        $user = $this->security->getUser();

        $discussions = $this->searchDiscussions->findDiscussions($user, $criteria, $saveSearch);

        return $this->pagination->getPaginationDiscussion(
            $discussions['discussions'],
            $page,
            $discussions['countActiveDiscussions'] + self::LIMIT
        );
    }

    public function searchMessagesNavBar(): array
    {
        $user = $this->security->getUser();

        return $this->searchDiscussions->findMessagesNavBar($user);
    }

    private function handleInvalidForm(FormInterface $discussionForm) : JsonResponse
    {
        return new JsonResponse([
            'code' => Discussion::DISCUSSION_INVALID_FORM,
            'errors' => $this->getErrorMessages($discussionForm)
        ]);
    }

    private function getErrorDiscussion(FormInterface $discussionForm): array
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