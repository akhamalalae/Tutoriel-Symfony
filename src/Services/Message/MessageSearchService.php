<?php

namespace App\Services\Message;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Pagination\Pagination;
use App\Controller\Messaging\Search\Message\SearchMessages;

class MessageSearchService
{
    private const LIMIT = 5;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SearchMessages $searchMessages, 
        private readonly Pagination $pagination
    ) {}

    /**
     * Save search criteria for messages
     *
     * @param User $user The authenticated user
     * @param array|null $criteria The search criteria
     * @return array Contains the search message entity and save flag
     * @throws \InvalidArgumentException When criteria are invalid
     */
    public function saveSearch(User $user, ?array $criteria): array
    {
        if (!$criteria) {
            return [
                'searchMessage' => null,
                'saveSearch' => false
            ];
        }

        $this->validateCriteria($criteria);

        $searchMessage = new SearchMessage();
        $searchMessage->setCreatorUser($user)
            ->setCreatedThisMonth($criteria['createdThisMonth'] === 'true')
            ->setDateCreation(new \DateTime())
            ->setFileName($criteria['fileName'])
            ->setMessage($criteria['message'])
            ->setDescription($criteria['description']);

        $this->em->persist($searchMessage);
        $this->em->flush();

        return [
            'searchMessage' => $searchMessage,
            'saveSearch' => $criteria['saveSearch'] === 'true'
        ];
    }

    /**
     * Get pagination information for messages
     *
     * @param User $user The authenticated user
     * @param Discussion $discussion The discussion to search in
     * @param int $page The current page number
     * @param SearchMessage|null $criteria The search criteria
     * @param bool $saveSearch Whether to save the search
     * @return array The pagination information
     */
    public function messagesPaginationInfos(
        User $user, 
        Discussion $discussion, 
        int $page, 
        ?SearchMessage $criteria, 
        bool $saveSearch
    ): array {
        return $this->pagination->getPagination(
            $this->searchMessages->findMessages($user, $discussion, $criteria, $saveSearch),
            $page,
            self::LIMIT
        );
    }

    /**
     * Validate search criteria
     *
     * @param array $criteria The criteria to validate
     * @throws \InvalidArgumentException When criteria are invalid
     */
    private function validateCriteria(array $criteria): void
    {
        $requiredFields = ['saveSearch', 'createdThisMonth', 'fileName', 'message', 'description'];
        
        foreach ($requiredFields as $field) {
            if (!isset($criteria[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }
}