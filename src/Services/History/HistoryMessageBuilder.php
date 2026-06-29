<?php

namespace App\Services\History;

use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\User\UserService;

class HistoryMessageBuilder
{
    public function __construct(
        private TranslatorInterface $translator,
        private UserService $userService
    ) {}

    public function build(array $changes): string
    {
        if (empty($changes)) {
            return '';
        }

        $lines = [];

        foreach ($changes as $field => $change) {

            // ignorer champs techniques
            if (in_array($field, [
                'createdAt',
                'updatedAt',
                'resolvedAt',
                'closedAt'
            ])) {
                continue;
            }

            $old = $this->formatValue($change['old'] ?? null);
            $new = $this->formatValue($change['new'] ?? null);

            // éviter faux changements
            if ($old === $new) {
                continue;
            }

            $lines[] = $this->formatLine($field, $old, $new);
        }

        if (empty($lines)) {
            return '';
        }

        return sprintf(
            "Bonjour,\n\nUne mise à jour du ticket :\n\n%s\n\nCordialement,",
            implode("\n", $lines)
        );
    }

    // ================= FORMAT LIGNE =================

    private function formatLine(string $field, string $old, string $new): string
    {
        $labels = [
            // BUSINESS
            'status' => $this->translator->trans('ticket.field.status'),
            'type' => $this->translator->trans('ticket.field.type'),
            'priority' => $this->translator->trans('ticket.field.priority'),

            // USERS
            'assignedTo' => $this->translator->trans('ticket.field.assigned_to'),
            'targetUser' => $this->translator->trans('ticket.field.target_user'),
            'createdBy' => $this->translator->trans('ticket.field.created_by'),

            // CONTENT
            'title' => $this->translator->trans('ticket.field.title'),
            'description' => $this->translator->trans('ticket.field.description'),

            // COMPANY
            'company' => $this->translator->trans('ticket.field.company'),
        ];

        $label = $labels[$field] ?? ucfirst($field);

        return sprintf('• %s : %s → %s', $label, $old, $new);
    }

    // ================= FORMAT VALEUR =================

    private function formatValue($value): string
    {
        if ($value === null) {
            return '-';
        }

        // ================= ENUM =================
        if ($value instanceof \BackedEnum) {
            return $this->translator->trans(
                'ticket.enum.' . strtolower($value->name)
            );
        }

        // ================= USER =================
        if (is_object($value) && method_exists($value, 'getSensitiveDataFullName')) {
            return $value->getSensitiveDataFullName();
        }

        // ================= GENERIC ENTITY =================
        if (is_object($value)) {

            if (method_exists($value, 'getName')) {
                return $value->getName();
            }

            if (method_exists($value, 'getTitle')) {
                return $value->getTitle();
            }

            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
        }

        // ================= DATE =================
        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        // ================= BOOL =================
        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }

        // ================= ARRAY / JSON =================
        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }
}