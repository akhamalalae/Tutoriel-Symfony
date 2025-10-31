<?php

namespace App\Services\Discussion;

use App\Controller\Messaging\Search\Discussion\SearchDiscussions;
use App\Controller\Pagination\Pagination;
use App\Services\User\UserService;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;

class DiscussionSearchService
{
    const LIMIT = 2;

    public function __construct(
        private SearchDiscussions $searchDiscussions,
        private Pagination $pagination,
        private UserService $userService,
        private EntityManagerInterface $em
    ) {}
    
    /**
     * Get pagination information for discussions
     *
     * @param int $page The current page number
     * @param SearchDiscussion|null $criteria The search criteria
     *
     * @return array The pagination information
     */
    public function discussions(int $page, SearchDiscussion|null $criteria): array
    {
        $user = $this->userService
            ->getAuthenticatedUser();

        return $this->pagination
            ->paginationDiscussion($page, self::LIMIT, $criteria);
    }

    /**
     * Save search criteria for a user
     *
     * @param array|null $criteria The search criteria
     *
     * @return SearchDiscussion|null The saved SearchDiscussion entity or null
     * 
     * @throws \InvalidArgumentException When criteria are invalid
     */
    public function saveSearch(?array $criteria): ?SearchDiscussion
    {
        if (empty($criteria)) {
            return null;
        }

        $this->validateCriteria($criteria);

        $user               = $this->userService->getAuthenticatedUser();
        $saveSearch         = $criteria['saveSearch'] === 'true' ? true : false;
        $firstName          = $criteria['firstName'] ?? '';
        $name               = $criteria['name'] ?? '';
        $description        = $criteria['description'] ?? '';
        $createdThisMonth   = $criteria['createdThisMonth'] == 'true' ? true : false;
        $IdSearchDiscussion = $criteria['IdSelectedSearchDiscussion'] ?? '';

        // Cherche une entité existante
        $existing = $this->em->getRepository(SearchDiscussion::class)->find($IdSearchDiscussion);

        $searchDiscussion = $existing ?: new SearchDiscussion();

        $searchDiscussion->setCreatorUser($user)
            ->setCreatedThisMonth($createdThisMonth)
            ->setDateCreation(new \DateTime())
            ->setName($name)
            ->setFirstName($firstName)
            ->setDescription($description)
            ->setSensitiveDataName($name)
            ->setSensitiveDataFirstName($firstName);

        // Persiste uniquement si c'est une nouvelle entité
        if (!$existing && $description !== '' && $saveSearch) {
            $this->em->persist($searchDiscussion);
        }

        // Flush si saveSearch activé et description renseignée
        if ($description !== '' && $saveSearch) {
            $this->em->flush();
        }

        return $searchDiscussion;
    }

    /**
     * Validate search criteria
     *
     * @param array $criteria The criteria to validate
     * @throws \InvalidArgumentException When criteria are invalid
     */
    private function validateCriteria(array $criteria): void
    {
        $requiredFields = ['saveSearch', 'createdThisMonth', 'name', 'description'];
        
        foreach ($requiredFields as $field) {
            if (!isset($criteria[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }
}