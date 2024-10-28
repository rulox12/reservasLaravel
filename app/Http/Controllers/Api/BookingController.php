<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\ReservationConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'bookings' => Booking::with(['user', 'room'])->get()
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_price' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking = Booking::create([
            'user_id' => $validatedData['user_id'],
            'room_id' => $validatedData['room_id'],
            'check_in_date' => $validatedData['check_in_date'],
            'check_out_date' => $validatedData['check_out_date'],
            'total_price' => $validatedData['total_price'],
            'status' => $validatedData['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully!',
            'booking' => $booking
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_price' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully!',
            'booking' => $booking
        ]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createReservation(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'totalPrice' => 'required|numeric',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $isRoomAvailable = $this->isRoomAvailable(
            $validatedData['room_id'],
            $validatedData['startDate'],
            $validatedData['endDate']
        );

        if (!$isRoomAvailable) {
            return response()->json([
                'message' => 'La habitación no está disponible en el rango de fechas seleccionado.',
            ], 422);
        }

        $user = $this->findOrCreateUser($validatedData['email'], $validatedData['name']);

        $reservation = Booking::create([
            'user_id' => $user->id,
            'room_id' => $request->input('room_id'),
            'check_in_date' => $validatedData['startDate'],
            'check_out_date' => $validatedData['endDate'],
            'total_price' => $validatedData['totalPrice'],
            'status' => 'pending',
        ]);

        $user->notify(new ReservationConfirmed($reservation));

        return response()->json([
            'message' => 'Reserva confirmada',
            'reservation' => $reservation,
        ], 201);
    }

    private function findOrCreateUser(string $email, string $name): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'email' => $email,
                'name' => $name,
                'password' => bcrypt($name),
                'role' => 'client',
            ]);
        }

        return $user;
    }

    private function isRoomAvailable(int $roomId, string $startDate, string $endDate): bool
    {
        return !Booking::where('room_id', $roomId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                    ->orWhereBetween('check_out_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
}
