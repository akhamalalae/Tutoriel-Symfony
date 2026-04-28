<?php

namespace App\SuggestionsTerme;

/**
 * Définit le contrat d’un service de suggestions.
 */
interface IAmTheTestInterface
{
    /**
     * Retourne les meilleures suggestions pour un terme donné.
     *
     * @param string $term Terme recherché
     * @param string[] $choices Liste de termes possibles
     * @param int $numberOfSuggestions Nombre de suggestions attendues
     *
     * @return string[] Liste des suggestions
     */
    public function getSuggestions(
        string $term,
        array $choices,
        int $numberOfSuggestions
    ): array;
}
