<?php

namespace App\Factory;

class SharedFixtureData
{
    private static ?\DateTimeImmutable $startDate = null;
    private static int $counter = 0;

    public static function getNextDate(): \DateTimeImmutable
    {
        if (!self::$startDate) {
            self::$startDate = new \DateTimeImmutable('2000-01-01');
        }

        // Calcule la date progressive
        $date = self::$startDate->modify('+' . self::$counter . ' days');

        // Si la date dépasse "maintenant", on force à "now"
        $now = new \DateTimeImmutable();
        if ($date > $now) {
            $date = $now;
        }

        self::$counter++;

        return $date;
    }
}