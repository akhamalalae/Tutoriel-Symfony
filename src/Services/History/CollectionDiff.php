<?php

namespace App\Services\History;

class CollectionDiff
{
    public function diff(array $old, array $new): array
    {
        dump('old s', $old);
        dump('old s', $new);
        $oldById = [];
        foreach ($old as $item) {
            $oldById[(int)$item->getId()] = $item;
        }

        $newById = [];
        foreach ($new as $item) {
            if ($item->getId()) {
                $newById[(int)$item->getId()] = $item;
            }
        }

        return [
            'added' => array_diff_key($newById, $oldById),
            'removed' => array_diff_key($oldById, $newById),
            'common' => array_intersect_key($oldById, $newById),
        ];
    }
}