<?php

namespace App\Services\History;

class EntityChangeDetector
{
    public function detectChanges(object $old, object $new): array
    {
        $changes = [];

        $reflection = new \ReflectionClass($old);

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            $oldValue = $property->getValue($old);
            $newValue = $property->getValue($new);

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