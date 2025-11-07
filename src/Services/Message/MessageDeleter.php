<?php
namespace App\Services\Message;

use App\Contracts\Message\MessageDeleterInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DiscussionMessageUser;
use App\Entity\SearchMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageDeleter implements MessageDeleterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {}

    public function deleteMessage(int $id): JsonResponse
    {
        $entity = $this->em
            ->getRepository(DiscussionMessageUser::class)->find($id);

        if (!$entity) {
            return new JsonResponse(['message' => $this->translator->trans('Message not found')], 404);
        }

        foreach ($entity->getMessage()->getFileMessages() as $file) {
            $this->em->remove($file);
        }

        foreach ($entity->getAnswerMessages() as $answer) {
            $this->em->remove($answer);
        }

        $this->em->remove($entity->getMessage());
        $this->em->remove($entity);
        $this->em->flush();

        return new JsonResponse(['message' => $this->translator->trans('Deleted successfully')]);
    }

    public function deleteSearchMessage(int $id): JsonResponse
    {
        $search = $this->em
            ->getRepository(SearchMessage::class)->find($id);
            
        if (!$search) {
            return new JsonResponse(['message' => $this->translator->trans('Search not found')], 404);
        }

        $this->em->remove($search);
        $this->em->flush();

        return new JsonResponse(['message' => $this->translator->trans('Deleted successfully')]);
    }
}
