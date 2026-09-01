<?php

namespace App\Services\History;

use Doctrine\Common\Collections\Collection;

class EntityChangeDetector
{
    private const SKIPPED_PROPERTIES = [
        'ticketEquipment',
        'ticketSoftware',
        'ticketMessages',
    ];

    public function detectChanges(object $old, object $new): array
    {
        $changes = [];

        $reflection = new \ReflectionClass($old);

        foreach ($reflection->getProperties() as $property) {
            if (in_array($property->getName(), self::SKIPPED_PROPERTIES, true)) {
                continue;
            }

            $property->setAccessible(true);

            $oldValue = $property->getValue($old);
            $newValue = $property->getValue($new);

            if ($oldValue instanceof Collection || $newValue instanceof Collection) {
                continue;
            }

            if ($oldValue != $newValue) {
                $changes[$property->getName()] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}