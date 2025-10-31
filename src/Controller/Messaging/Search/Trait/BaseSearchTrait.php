<?php

namespace App\Controller\Messaging\Search\Trait;

use Elastica\Query\BoolQuery;
use DateTimeImmutable;
use Elastica\Query\Range;
use Elastica\Query\QueryString;

trait BaseSearchTrait
{
    private const BOOST_EXACT = 1.0;   // phrase exacte
    private const BOOST_PARTIAL = 0.8; // recherche par mots
    private const BOOST_PREFIX = 0.9;  // recherche par préfixe

    /**
     * Multi-term search with group separation via ";"
     * 
     * @param BoolQuery $multiFieldGroup The BoolQuery to add the search terms to
     * @param string $field The field to search in
     * @param string $value The search value containing multiple terms separated by ";"
     * 
     * @return BoolQuery The updated BoolQuery with added search terms
     *
     * Exemple :
     *   "term 1 term 2; term 3"
     * lancera deux recherches :
     *   - "term 1 term 2"
     *   - "term 3"
     */
    private function multiTermSearchQuery(BoolQuery $multiFieldGroup, string $field, string $value): BoolQuery
    {
        // Nettoyage et découpage par “;”
        $groups = array_filter(array_map('trim', explode(';', (string) $value)));

        if (empty($groups)) {
            return $multiFieldGroup;
        }

        foreach ($groups as $group) {
            if ($group === '') {
                continue;
            }

            // Recherche par phrase exacte
            $exactQuery = new QueryString('"' . $group . '"');
            $exactQuery->setFields([$field]);
            $exactQuery->setBoost(self::BOOST_EXACT);
            $multiFieldGroup->addShould($exactQuery);

            // Recherche "commence par ..."
            $prefixQuery = new QueryString($group . '*');
            $prefixQuery->setFields([$field]);
            $prefixQuery->setParam('default_operator', 'AND');
            $prefixQuery->setParam('fuzziness', 'AUTO');
            $prefixQuery->setBoost(self::BOOST_PREFIX);
            $multiFieldGroup->addShould($prefixQuery);

            // Recherche "contient ..."
            $containsQuery = new QueryString('*' . $group . '*');
            $containsQuery->setFields([$field]);
            $containsQuery->setParam('default_operator', 'AND');
            $containsQuery->setParam('fuzziness', 'AUTO');
            $containsQuery->setBoost(self::BOOST_PARTIAL);
            $multiFieldGroup->addShould($containsQuery);
        }

        // Au moins un des groupes doit correspondre
        $multiFieldGroup->setMinimumShouldMatch(1);

        return $multiFieldGroup;
    }

    /**
     * Add date range filter for current month
     */
    private function addDateRangeFilter(BoolQuery $boolQuery, string $field): void
    {
        $startOfMonth = new DateTimeImmutable('first day of this month');
        $endOfMonth = new DateTimeImmutable('last day of this month');

        $range = new Range($field, [
            'gte' => $startOfMonth->format('Y-m-d'),
            'lte' => $endOfMonth->format('Y-m-d')
        ]);

        $boolQuery->addFilter($range);
    }
}
