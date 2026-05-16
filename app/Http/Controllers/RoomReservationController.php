<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

use App\Models\Room;
use App\Services\AllocateRoomService;
use App\Helpers\TravelTimeHelper;

class RoomReservationController extends Controller
{
    public $errorStatus = 500;
    public $successStatus = 200;
    public $errorUnAuthorisedStatus = 401;

    
    public function allRooms(Request $request)
    {
        $data = Room::orderBy('floor_number', 'desc')
            ->orderBy('room_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Rooms fetched successfully.',
            'data' => $data
        ], $this->successStatus);
    }


    public function bookRooms(Request $request)
    {
    try {

        $validator = Validator::make($request->all(), [

            'rooms' => 'required|integer|min:1|max:5',

        ], [

            'rooms.required' => 'Room count is required.',
            'rooms.integer' => 'Room count must be number.',
            'rooms.min' => 'Minimum 1 room required.',
            'rooms.max' => 'Maximum 5 rooms allowed.'

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $requiredRooms = (int) $request->rooms;

        $availableRooms = Room::where('is_booked', false)->get();

        if ($availableRooms->count() < $requiredRooms) {

            return response()->json([
                    'success' => false,
                    'message' => 'Rooms unavailable.'
                ], 400);
            }
    
            $service = new AllocateRoomService();
    
            $allocatedRooms = collect(
                $service->allocate($requiredRooms)
            );
            if ($allocatedRooms->count() == 0) {
    
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to allocate rooms.'
                ], 400);
            }
    
            $roomIds = $allocatedRooms->pluck('id')->toArray();
    
            Room::whereIn('id', $roomIds)
                ->update([
                    'is_booked' => true
                ]);
    
            $roomNumbers = $allocatedRooms
                ->pluck('room_number')
                ->toArray();
    
            $travelTime = TravelTimeHelper::calculate($roomNumbers);
    
            return response()->json([
                'success' => true,
                'message' => 'Rooms booked successfully.',
                'data' => [
                    'rooms' => $roomNumbers,
                    'travel_time' => $travelTime
                ]
            ], 200);
    
        } catch (\Throwable $e) {
    
            \Log::error('Room Booking Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function randomOccupancy(Request $request)
    {
        $rooms = Room::get();

        foreach ($rooms as $room) {

            Room::where('id', $room->id)
                ->update([
                    'is_booked' => rand(0, 1)
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Random occupancy generated successfully.'
        ], $this->successStatus);
    }

    public function resetAllBookings(Request $request)
    {
        Room::query()->update([
            'is_booked' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hotel reset successfully.'
        ], $this->successStatus);
    }
}
