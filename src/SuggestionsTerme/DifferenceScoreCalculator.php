<?php

namespace App\SuggestionsTerme;

/**
 * Calcule le nombre de lettres à remplacer
 * entre deux chaînes de même longueur.
 */
class DifferenceScoreCalculator
{
    public function calculate(string $a, string $b): int
    {
        $score = 0;

        for ($i = 0; $i < strlen($a); $i++) {
            if ($a[$i] !== $b[$i]) {
                $score++;
            }
        }

        return $score;
    }
}
