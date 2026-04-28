<?php

namespace App\Services\History;

use App\Entity\HistoryLog;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;

class HistoryLogger
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserService $userService
    ) {}

    public function log(
        object $entity,
        string $action,
        ?string $field = null,
        $oldValue = null,
        $newValue = null
    ): void {
        $log = new HistoryLog();

        $log->setEntityType($entity::class);
        $log->setEntityId($entity->getId());
        $log->setAction($action);
        $log->setField($field);
        $log->setOldValue($this->normalize($oldValue));
        $log->setNewValue($this->normalize($newValue));
        $log->setUser($this->userService->getAuthenticatedUser());
        $log->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($log);
        dump($log);
    }

    private function normalize($value): ?array
    {
        if ($value === null) {
            return null;
        }

        // enum
        if ($value instanceof \BackedEnum) {
            return [
                'value' => $value->value,
                'name' => $value->name,
                'class' => $value::class,
            ];
        }

        // scalar → wrap
        if (is_scalar($value)) {
            return ['value' => $value];
        }

        // DateTime
        if ($value instanceof \DateTimeInterface) {
            return ['value' => $value->format('Y-m-d H:i:s')];
        }

        // Entity avec ID
        if (is_object($value) && method_exists($value, 'getId')) {
            return [
                'id' => $value->getId(),
                'class' => $value::class
            ];
        }

        // fallback
        return ['value' => (string) $value];
    }
}