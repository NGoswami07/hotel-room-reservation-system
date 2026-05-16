<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\RoomReservationController;

Route::get('/all-rooms', [RoomReservationController::class, 'allRooms']);
Route::post('/book-rooms', [RoomReservationController::class, 'bookRooms']);
Route::post('/random-occupancy', [RoomReservationController::class, 'randomOccupancy']);
Route::post('/reset-all-bookings', [RoomReservationController::class, 'resetAllBookings']);

