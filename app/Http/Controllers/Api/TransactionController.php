<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Transaction::with('user', 'event');
        $user = Auth::user();

        if ($user->role === 'organizer') {
            $query->whereHas('event', function ($q) {
                $q->where('user_id', Auth::id());
            });
        } elseif ($user->role === 'user') {
            $query->where('user_id', Auth::id());
        }

        $transaction = $query->get();

        return response()->json([
            'data' => $transaction,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        if (!($event->stock >= $request->quantity)) {
            return response()->json([
                'message' => 'Stok tidak cukup',
            ], 422);
        }

        if (now()->gt($event->start_event)) {
            return response()->json([
                'message' => 'Tiket tidak bisa dibeli lagi karna event sudah mulai'
            ], 200);
        }

        $totalPrice = $event->price * $validated['quantity'];

        $transaction = Transaction::create([
            'user_id'     => Auth::id(),
            'event_id'    => $event->id,
            'quantity'    => $validated['quantity'],
            'total_price' => $totalPrice,
            'status'      => 'paid',
        ]);

        $event->decrement('stock', $validated['quantity']);

        return response()->json([
            'message' => 'Transaksi berhasil dilakukan',
            'data'    => $transaction->load('event')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $user = Auth::user();

        if ($user->role === 'user' && $transaction->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        if ($user->role === 'organizer' && $transaction->event->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $transaction->load('user');

        return response()->json([
            'data' => $transaction,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:waiting,paid,cancelled,expired',
        ]);

        try {
            $updatedTransaction = DB::transaction(function () use ($id, $validated) {
                $transaction = Transaction::with('event')
                    ->lockForUpdate()
                    ->findOrFail($id);
                if ($validated['status'] === 'cancelled' && $transaction->status !== 'cancelled') {
                    $transaction->update(['status' => 'cancelled']);
                    $transaction->event->increment('stock', $transaction->quantity);
                } else {
                    $transaction->update(['status' => $validated['status']]);
                }

                return $transaction;
            });

            return response()->json([
                'message' => 'Status transaksi berhasil diperbarui',
                'data' => $updatedTransaction->load('event')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui transaksi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'paid' || $transaction->status === 'waiting') {
            $transaction->event->increment('stock', $transaction->quantity);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus',
        ], 200);
    }
}
