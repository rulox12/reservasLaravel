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
            'message' => '¡Habitación creada con éxito!',
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
            'message' => '¡Habitación actualizada con éxito!',
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
                'message' => '¡Habitación eliminada con éxito!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la habitación: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function availableRooms(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'startDate' => 'required|date|after_or_equal:today',
                'endDate' => 'required|date|after:startDate',
                'capacity' => 'required|integer|min:1',
                'airConditioning' => 'required',
            ]);

            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $capacity = $request->input('capacity');
            $airConditioning = $request->input('airConditioning');

            $availableRooms = Room::whereDoesntHave('bookings', function ($query) use ($startDate, $endDate) {
                $query->where('check_in_date', '<', $endDate)
                    ->where('check_out_date', '>', $startDate);
            });

            $availableRooms->where('status', 'available');

            if ($capacity) {
                $availableRooms->where('capacity', '=', $capacity);
            }

            if (!is_null($airConditioning)) {
                $availableRooms->where('climate_control', $airConditioning);
            }

            $availableRooms = $availableRooms->get();

            return response()->json([
                'success' => true,
                'message' => 'Habitaciones disponibles encontradas.',
                'rooms' => $availableRooms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar habitaciones: ' . $e->getMessage(),
                'rooms' => []
            ], 500);
        }
    }

    public function search(Request $request)
    {
        // Validamos los parámetros de búsqueda
        $request->validate([
            'room_number' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:available,reserved,occupied',
            'capacity' => 'nullable|in:2,4,8',
        ]);

        // Obtenemos los parámetros de búsqueda
        $room_number = $request->input('room_number');
        $status = $request->input('status');
        $capacity = $request->input('capacity');

        // Construimos la consulta de búsqueda
        $rooms = Room::query();

        // Filtrar por número de habitación si está presente
        if ($room_number) {
            $rooms->where('room_number', 'like', '%' . $room_number . '%');
        }

        // Filtrar por estado si está presente
        if ($status) {
            $rooms->where('status', $status);
        }

        // Filtrar por capacidad si está presente
        if ($capacity) {
            $rooms->where('capacity', $capacity);
        }

        // Ejecutamos la consulta y obtenemos los resultados
        $rooms = $rooms->get();

        // Retornamos los resultados como una respuesta JSON
        return response()->json(['rooms' => $rooms]);
    }
}
