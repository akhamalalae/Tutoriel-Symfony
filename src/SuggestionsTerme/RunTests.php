<?php
namespace App\SuggestionsTerme;

use App\SuggestionsTerme\TestSuggestionService;

/**
 * Test 1 – Exemple fourni :
 * Le retour sera donc : gros, gras
 * 
 * Test 2 – Égalité score même longueur :
 * Le retour sera donc : abd, abe, abf
 * 
 * Test 3 – Aucun mot valide :
 * Le retour sera donc vide (aucune suggestion valide)
 * 
 * Test 4 – Mot exact absent :
 * Le retour sera donc : char, chut
 */
class RunTests
{
    public function runTest(): void
    {
        // Instanciation et exécution des tests
        $test = new TestSuggestionService();

        $test->run();

        die();
    }
}



