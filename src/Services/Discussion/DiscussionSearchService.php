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

    public function discussions(int $page, SearchDiscussion|null $criteria, bool $saveSearch): array
    {
        $user = $this->userService->getAuthenticatedUser();

        $discussions = $this->searchDiscussions->findDiscussions($user, $criteria, $saveSearch);

        return $this->pagination->getPaginationDiscussion(
            $discussions['discussions'],
            $page,
            $discussions['countActiveDiscussions'] + self::LIMIT
        );
    }

    public function searchMessagesNavBar(): array
    {
        $user = $this->userService->getAuthenticatedUser();

        return $this->searchDiscussions->findMessagesNavBar($user);
    }

    public function saveSearchDiscussion(?array $criteria): array 
    {
        $searchDiscussion = null;

        $saveSearch = false;

        if ($criteria) {
            $user = $this->userService->getAuthenticatedUser();
        
            $saveSearch = $criteria['saveSearch'];

            $searchDiscussion = new SearchDiscussion();

            $name = $criteria['name'];

            $firstName = $criteria['firstName'];

            $createdThisMonth = $criteria['createdThisMonth'] == 'true' ? true : false;

            $description = $criteria['description'];

            $searchDiscussion->setCreatorUser($user)
                ->setCreatedThisMonth($createdThisMonth)
                ->setDateCreation(new \DateTime())
                ->setName($name)
                ->setFirstName($firstName)
                ->setDescription($description)
            ;

            $this->em->persist($searchDiscussion);

            $this->em->flush();
        }

        return [
            'searchDiscussion' => $searchDiscussion,
            'saveSearch' => $saveSearch == 'true' ? true : false
        ];
    }
}