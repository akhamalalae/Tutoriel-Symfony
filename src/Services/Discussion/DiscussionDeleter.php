<?php
namespace App\Services\Discussion;

use App\Contracts\Discussion\DiscussionDeleterInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DiscussionMessageUser;
use App\Entity\SearchDiscussion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Contracts\Error\ErrorResponseInterface;

class DiscussionDeleter implements DiscussionDeleterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly ErrorResponseInterface $errorResponseService
    ) {}

    public function deleteDiscussion(int $id): JsonResponse
    {
        $discussion = $this->em->getRepository(Discussion::class)->find($id);

        if (!$discussion) {
            return new JsonResponse(['message' => 'Message not found'], 404);
        }

        $discussionMessageUsers = $discussion->getDiscussionMessageUsers();
        
        foreach ($discussionMessageUsers as $item) {
            $this->em->remove($item);
        }

        $this->em->remove($discussion);
        $this->em->flush();

        return new JsonResponse(['message' => $this->translator->trans('Element deleted successfully')], 200);
    }

    public function deleteSearchDiscussion(int $id): JsonResponse
    {
        try {
            $searchDiscussion = $this->em->getRepository(SearchDiscussion::class)->find($id);

            $this->em->remove($searchDiscussion);
            $this->em->flush();

            return new JsonResponse(['message' => $this->translator->trans('Element deleted successfully')], 200);
        } catch (\Exception $e) {
            return $this->errorResponseService->createErrorResponse($e);
        }
    }
}
