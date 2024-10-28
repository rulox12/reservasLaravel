<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::post('/room', [RoomController::class, 'store'])->name('room.store');
Route::put('/room/{id}', [RoomController::class, 'update'])->name('room.update');
Route::delete('/room/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
Route::get('/available-rooms', [RoomController::class, 'availableRooms'])->name('room.available.rooms');

Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('booking.update');
Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
Route::delete('/bookings/reservation', [BookingController::class, 'destroy'])->name('booking.destroy');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');