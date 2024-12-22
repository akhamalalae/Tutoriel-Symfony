<?php

namespace App\Security\Messaging;

use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class DiscussionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Environment $environment,
        private Security $security,
        private ParameterBagInterface $parameters
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

        $discussion->setPersonOne($user)
            ->setPersonTwo($discussion->getPersonTwo())
            ->setCreatorUser($user)
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime());

        $this->em->persist($discussion);

        $this->em->flush();

        return new JsonResponse([
            'code' => Discussion::DISCUSSION_ADDED_SUCCESSFULLY,
            'html' => $this->environment->render('message/discussions.html.twig', [
                'item' => $discussion
            ])
        ]);
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