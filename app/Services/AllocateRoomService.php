<?php

namespace App\Services;

use App\Models\Room;
use App\Helpers\TravelTimeHelper;

class AllocateRoomService
{
    public function allocate($requiredRooms)
    {
        $rooms = Room::where('is_booked', false)
            ->orderBy('floor_number')
            ->orderBy('room_number')
            ->get();

    
        $groupedFloors = $rooms->groupBy('floor_number');

    
        foreach ($groupedFloors as $floorRooms) {

            if ($floorRooms->count() >= $requiredRooms) {

                return $floorRooms
                    ->sortBy('room_number')
                    ->take($requiredRooms)
                    ->values();
            }
        }

    
        $bestCombination = collect([]);

        $bestTravelTime = PHP_INT_MAX;

        for ($i = 0; $i <= $rooms->count() - $requiredRooms; $i++) {

            $combo = $rooms
                ->slice($i, $requiredRooms)
                ->values();

            $roomNumbers = $combo
                ->pluck('room_number')
                ->toArray();

            $travelTime = TravelTimeHelper::calculate($roomNumbers);

            if ($travelTime < $bestTravelTime) {

                $bestTravelTime = $travelTime;

                $bestCombination = $combo;
            }
        }

        return $bestCombination;
    }
    
    private function combinations($array, $size)
    {
        $result = [];

        $this->combine(
            $array,
            $size,
            0,
            [],
            $result
        );

        return $result;
    }

    private function combine(
        $array,
        $size,
        $start,
        $path,
        &$result
    ) {

        if (count($path) == $size) {

            $result[] = $path;

            return;
        }

        for ($i = $start; $i < count($array); $i++) {

            $newPath = $path;

            $newPath[] = $array[$i];

            $this->combine(
                $array,
                $size,
                $i + 1,
                $newPath,
                $result
            );
        }
    }
}