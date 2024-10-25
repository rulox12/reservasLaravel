<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'rooms' => Room::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms',
            'type' => 'required|in:single,double,suite',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:available,reserved,occupied',
            'capacity' => 'required|in:2,4,8',
            'climate_control' => 'required|in:fan,air_conditioning',
        ]);

        $room = Room::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully!',
            'room' => $room
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->ignore($id),
            ],
            'type' => 'required|in:single,double,suite',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:available,reserved,occupied',
            'capacity' => 'required|in:2,4,8',
            'climate_control' => 'required|in:fan,air_conditioning',
        ]);

        $room = Room::findOrFail($id);

        $room->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully!',
            'room' => $room
        ]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting room: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function availableRooms(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'startDate' => 'required|date|after_or_equal:today',
                'endDate' => 'required|date|after:startDate',
                'capacity' => 'nullable|integer|min:1',
                'airConditioning' => 'nullable',
            ]);

            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $capacity = $request->input('capacity');
            $airConditioning = $request->input('airConditioning');

            $availableRooms = Room::whereDoesntHave('bookings', function ($query) use ($startDate, $endDate) {
                $query->where(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('check_in_date', '<', $endDate)
                        ->where('check_out_date', '>', $startDate);
                });
            });

            if ($capacity) {
                $availableRooms->where('capacity', '>=', $capacity);
            }

            if ($airConditioning !== null) {
                $availableRooms->where('climate_control', $airConditioning ? 'air_conditioning' : 'fan');
            }

            $availableRooms = $availableRooms->get();

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully!',
                'rooms' => $availableRooms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar habitación: ' . $e->getMessage(),
                'rooms' => []
            ], 500);
        }
    }
}
