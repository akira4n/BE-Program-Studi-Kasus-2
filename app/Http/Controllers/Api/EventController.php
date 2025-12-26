<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $event = Event::all();

        return response()->json([
            'data' => $event,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'start_event' => 'required|date_format:Y-m-d',
            'price' => 'required|numeric',
        ]);

        $event = Event::create([
            'user_id' => Auth::id(),
            'stock' => $validated['capacity'],
            ...$validated,
        ]);

        return response()->json([
            'message' => 'Data event berhasil dibuat',
            'data' => $event,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            'data' => $event
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden: Anda tidak memiliki akses hak untuk mengupdate data ini',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer',
            'start_event' => 'sometimes|date_format:Y-m-d',
            'price' => 'sometimes|numeric',
        ]);

        if ($request->has('capacity')) {
            $soldTickets = $event->transactions()
                ->whereIn('status', ['paid', 'waiting'])
                ->sum('quantity');

            $newCapacity = $request->capacity;

            if ($newCapacity < $soldTickets) {
                return response()->json([
                    'message' => "Kapasitas baru ($newCapacity) tidak boleh lebih kecil dari tiket yang sudah terjual ($soldTickets)"
                ], 422);
            }

            $event->stock = $newCapacity - $soldTickets;
        }

        $event->update($validated);

        return response()->json([
            'message' => 'Data event berhasil diperbarui',
            'data' => $event,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden: Anda tidak memiliki akses hak untuk menghapus data ini',
            ], 403);
        }

        $event->delete();

        return response()->json([
            'message' => 'Data event berhasil dihapus'
        ], 200);
    }
}
