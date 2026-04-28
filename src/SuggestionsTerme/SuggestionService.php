<?php
namespace App\SuggestionsTerme;

use App\SuggestionsTerme\IAmTheTestInterface;
use App\SuggestionsTerme\DifferenceScoreCalculator;

/**
 * Implémentation du service de suggestions.
 */
class SuggestionService implements IAmTheTestInterface
{
    private DifferenceScoreCalculator $calculator;

    public function __construct()
    {
        $this->calculator = new DifferenceScoreCalculator();
    }

    /**
     * Calcule et retourne les meilleures suggestions
     */
    public function getSuggestions(
        string $term,
        array $choices,
        int $numberOfSuggestions
    ): array {
        $results = [];
        $termLength = strlen($term);

        foreach ($choices as $choice) {
            // On ignore les mots trop courts (pas d’insertion autorisée)
            if (strlen($choice) < $termLength) {
                continue;
            }

            $score = $this->calculator->calculate(
                $term,
                substr($choice, 0, $termLength)
            );

            $results[] = [
                'word' => $choice,
                'score' => $score,
                'lengthDiff' => abs(strlen($choice) - $termLength),
            ];
        }

        // Tri selon les règles métier
        usort($results, function ($a, $b) {
            return $a['score'] <=> $b['score']
                ?: $a['lengthDiff'] <=> $b['lengthDiff']
                ?: strcmp($a['word'], $b['word']);
        });

        return array_slice(
            array_column($results, 'word'),
            0,
            $numberOfSuggestions
        );
    }
}