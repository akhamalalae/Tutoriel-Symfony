<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlightExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', [$this, 'highlight'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Highlight search terms in the given text.
     *
     * @param string $text The text to search within.
     * @param string|null $search The search terms, separated by ";" for groups and spaces for words.
     *
     * @return string The text with highlighted search terms.
     */
    public function highlight(string $text, ?string $search): string
    {
        if (!$search) {
            return $text;
        }
    
        // Découpe par ";", puis par espace pour chaque groupe
        $groups = array_filter(array_map('trim', explode(';', $search)));
    
        foreach ($groups as $group) {
            $words = array_filter(preg_split('/\s+/', $group));
            foreach ($words as $word) {
                // Mot entier, insensible à la casse, sans toucher aux balises HTML existantes
                $pattern = '/(?<!>)\b(' . preg_quote($word, '/') . ')\b(?![^<]*>)/i';
                $text = preg_replace($pattern, '<strong style="color: #0d6efd;">$1</strong>', $text);

            }
        }
    
        return $text;
    }
}
