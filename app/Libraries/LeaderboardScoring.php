<?php

namespace App\Libraries;

final class LeaderboardScoring
{
    public const NON_COMPETITION_VERIFIED_POINTS = 50;

    public const PLACEMENT_POINTS = [
        1 => 100,
        2 => 75,
        3 => 50,
    ];

    public static function pointsForPlacement(?int $placement): int
    {
        return $placement === null ? 0 : (self::PLACEMENT_POINTS[$placement] ?? 0);
    }
}
