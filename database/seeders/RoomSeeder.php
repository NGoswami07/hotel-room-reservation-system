<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($floor = 1; $floor <= 9; $floor++) {
            for ($room = 1; $room <= 10; $room++) {
                Room::create([
                    'room_number' => ($floor * 100) + $room,
                    'floor_number' => $floor,
                    'is_booked' => false
                ]);
            }
        }

        for ($room = 1; $room <= 7; $room++) {
            Room::create([
                'room_number' => 1000 + $room,
                'floor_number' => 10,
                'is_booked' => false
            ]);
        }
    }
}
