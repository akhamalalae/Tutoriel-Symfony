<?php

namespace App\Services\History;

use App\Entity\TicketEquipment;
use App\Entity\TicketSoftware;

class CollectionDiff
{
    public function diff(array $old, array $new): array
    {
        $oldById = [];
        foreach ($old as $item) {
            if ($id = $item->getId()) {
                $oldById[(int) $id] = $item;
            }
        }

        $newById = [];
        $added = [];
        foreach ($new as $item) {
            if ($id = $item->getId()) {
                $newById[(int) $id] = $item;
            } else {
                $added[] = $item;
            }
        }

        return [
            'added' => array_merge($added, array_values(array_diff_key($newById, $oldById))),
            'removed' => array_values(array_diff_key($oldById, $newById)),
            'common' => array_intersect_key($oldById, $newById),
            'newById' => $newById,
        ];
    }

    /**
     * @param TicketEquipment[] $old
     * @param TicketEquipment[] $new
     */
    public function detectEquipmentChanges(array $old, array $new): array
    {
        if (!$this->hasEquipmentChanges($old, $new)) {
            return [];
        }

        return [
            'ticketEquipment' => [
                'old' => $this->summarizeEquipment($old),
                'new' => $this->summarizeEquipment($new),
            ],
        ];
    }

    /**
     * @param TicketSoftware[] $old
     * @param TicketSoftware[] $new
     */
    public function detectSoftwareChanges(array $old, array $new): array
    {
        if (!$this->hasSoftwareChanges($old, $new)) {
            return [];
        }

        return [
            'ticketSoftware' => [
                'old' => $this->summarizeSoftware($old),
                'new' => $this->summarizeSoftware($new),
            ],
        ];
    }

    /**
     * @param TicketEquipment[] $items
     */
    private function summarizeEquipment(array $items): string
    {
        if ($items === []) {
            return '-';
        }

        $lines = array_map(fn (TicketEquipment $item) => $this->formatEquipmentItem($item), $items);

        return implode('; ', $lines);
    }

    /**
     * @param TicketSoftware[] $items
     */
    private function summarizeSoftware(array $items): string
    {
        if ($items === []) {
            return '-';
        }

        $lines = array_map(fn (TicketSoftware $item) => $this->formatSoftwareItem($item), $items);

        return implode('; ', $lines);
    }

    /**
     * @param TicketEquipment[] $old
     * @param TicketEquipment[] $new
     */
    private function hasEquipmentChanges(array $old, array $new): bool
    {
        $diff = $this->diff($old, $new);

        if ($diff['added'] !== [] || $diff['removed'] !== []) {
            return true;
        }

        foreach ($diff['common'] as $id => $oldItem) {
            if ($this->equipmentItemChanged($oldItem, $diff['newById'][$id])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param TicketSoftware[] $old
     * @param TicketSoftware[] $new
     */
    private function hasSoftwareChanges(array $old, array $new): bool
    {
        $diff = $this->diff($old, $new);

        if ($diff['added'] !== [] || $diff['removed'] !== []) {
            return true;
        }

        foreach ($diff['common'] as $id => $oldItem) {
            if ($this->softwareItemChanged($oldItem, $diff['newById'][$id])) {
                return true;
            }
        }

        return false;
    }

    private function equipmentItemChanged(TicketEquipment $old, TicketEquipment $new): bool
    {
        return $old->getEquipmentModel()?->getId() !== $new->getEquipmentModel()?->getId()
            || $old->getComment() !== $new->getComment()
            || $old->getEquipmentAsset()?->getId() !== $new->getEquipmentAsset()?->getId()
            || $old->getStatus() != $new->getStatus();
    }

    private function softwareItemChanged(TicketSoftware $old, TicketSoftware $new): bool
    {
        return $old->getSoftwareProduct()?->getId() !== $new->getSoftwareProduct()?->getId()
            || $old->getComment() !== $new->getComment()
            || $old->getSoftwareLicense()?->getId() !== $new->getSoftwareLicense()?->getId()
            || $old->getStatus() != $new->getStatus();
    }

    private function formatEquipmentItem(TicketEquipment $item): string
    {
        $parts = [$item->getEquipmentModel()?->getName() ?? '?'];

        if ($item->getComment()) {
            $parts[] = $item->getComment();
        }

        if ($asset = $item->getEquipmentAsset()) {
            $parts[] = $asset->getSerialNumber() ?? $asset->getReference() ?? ('#' . $asset->getId());
        }

        return implode(' - ', $parts);
    }

    private function formatSoftwareItem(TicketSoftware $item): string
    {
        $parts = [$item->getSoftwareProduct()?->getName() ?? '?'];

        if ($item->getComment()) {
            $parts[] = $item->getComment();
        }

        if ($license = $item->getSoftwareLicense()) {
            $parts[] = $license->getLicenseKey() ?? ('licence #' . $license->getId());
        }

        return implode(' - ', $parts);
    }
}
