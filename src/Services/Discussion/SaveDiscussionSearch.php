<?php

namespace App\Services\Discussion;

use App\Services\User\UserService;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;
use App\Contracts\Discussion\SaveDiscussionSearchInterface;

class SaveDiscussionSearch implements SaveDiscussionSearchInterface
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $em
    ) {}

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
        if (!$existing && $description !== '' && $saveSearch === true) {
            $this->em->persist($searchDiscussion);
        }

        // Flush si saveSearch activé et description renseignée
        if ($description !== '' && $saveSearch === true) {
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