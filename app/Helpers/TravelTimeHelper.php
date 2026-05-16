<?php

namespace App\Helpers;

class TravelTimeHelper
{
    public static function calculate($rooms)
    {
        if (empty($rooms)) {
            return 0;
        }

        $rooms = array_map('intval', $rooms);
       
        sort($rooms, SORT_NUMERIC);

        $first = $rooms[0];

        $last = $rooms[count($rooms) - 1];

        $floor1 = intval($first / 100);

        $floor2 = intval($last / 100);

        $roomPos1 = $first % 100;

        $roomPos2 = $last % 100;

        $vertical = abs($floor1 - $floor2) * 2;

        $horizontal = abs($roomPos1 - $roomPos2);

        return $vertical + $horizontal;
    }
}