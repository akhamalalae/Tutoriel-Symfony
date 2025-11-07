<?php

namespace App\Services\Message;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;
use App\Contracts\Message\SaveMessageSearchInterface;
use DateTimeImmutable;

class SaveMessageSearch implements SaveMessageSearchInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
    ) {}

    /**
     * Save search criteria for a user
     *
     * @param User $user The user saving the search
     * @param array|null $criteria The search criteria
     *
     * @return SearchMessage|null The saved SearchMessage entity or null
     * 
     * @throws \InvalidArgumentException When criteria are invalid
     */
    public function saveSearch(User $user, ?array $criteria): ?SearchMessage
    {
        if (empty($criteria)) {
            return null;
        }

        $this->validateCriteria($criteria);

        $saveSearch         = ($criteria['saveSearch'] ?? 'false') === 'true';
        $fileName           = $criteria['fileName'] ?? '';
        $message            = $criteria['message'] ?? '';
        $description        = $criteria['description'] ?? '';
        $createdThisMonth   = ($criteria['createdThisMonth'] ?? 'false') === 'true';
        $IdSearchMessage    = $criteria['IdSelectedSearchMessage'] ?? '';

        // Cherche une entité existante
        $existing = $this->em->getRepository(SearchMessage::class)->find($IdSearchMessage);

        $searchMessage = $existing ?: new SearchMessage();

        $searchMessage->setCreatorUser($user)
            ->setCreatedThisMonth($createdThisMonth)
            ->setDateCreation(new \DateTime())
            ->setFileName($fileName)
            ->setMessage($message)
            ->setDescription($description)
            ->setSensitiveDataMessage($message)
            ->setSensitiveDataFileName($fileName);

        // Persiste uniquement si c'est une nouvelle entité
        if (!$existing && $description !== '' && $saveSearch == true) {
            $this->em->persist($searchMessage);
        }

        // Flush si saveSearch activé et description renseignée
        if ($description !== '' && $saveSearch == true) {
            $this->em->flush();
        }

        return $searchMessage;
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