<?php

namespace App\SuggestionsTerme;

use App\SuggestionsTerme\SuggestionService;

/**
 * Classe de tests pour SuggestionService.
 */
class TestSuggestionService
{
    private SuggestionService $service;

    public function __construct()
    {
        $this->service = new SuggestionService();
    }

    /**
     * Exécute tous les tests.
     */
    public function run(): void
    {
        $this->testExampleProvided();
        $this->testEqualScoreSameLength();
        $this->testNoValidSuggestions();
        $this->testExactMatchAbsent();
    }

    /**
     * Cas de test : exemple fourni dans l’énoncé.
     */
    private function testExampleProvided(): void
    {
        $choices = [
            'gros',
            'gras',
            'graisse',
            'agressif',
            'go',
            'ros',
            'gro'
        ];

        $result = $this->service->getSuggestions('gros', $choices, 2);

        dump("Test 1 – Exemple fourni :\n" . implode(', ', $result) . "\n\n");
    }

    /**
     * Cas de test : égalité parfaite de score et de longueur.
     */
    private function testEqualScoreSameLength(): void
    {
        $choices = ['abd', 'abe', 'abf'];

        $result = $this->service->getSuggestions('abc', $choices, 3);

        dump("Test 2 – Égalité score même longueur :\n" . implode(', ', $result) . "\n\n");
    }

    /**
     * Cas de test : aucun mot valide (tous trop courts).
     */
    private function testNoValidSuggestions(): void
    {
        $choices = ['te', 't'];

        $result = $this->service->getSuggestions('test', $choices, 2);

        dump("Test 3 – Aucun mot valide :\n" . implode(', ', $result) . "\n\n");
    }

    /**
     * Cas de test : mot exact absent, mais suggestions proches.
     */
    private function testExactMatchAbsent(): void
    {
        $choices = ['char', 'chut', 'chien'];

        $result = $this->service->getSuggestions('chat', $choices, 2);

        dump("Test 4 – Mot exact absent :\n" . implode(', ', $result) . "\n\n");
    }
}
