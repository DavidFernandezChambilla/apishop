<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $user = Auth::guard('api')->user();

        $orders = Order::with(['items.product', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.product_name' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            return DB::transaction(function () use ($request, $user) {
                // 1. Create Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'total_amount' => $request->total_amount,
                    'status' => 'pending',
                    'notes' => $request->notes
                ]);

                // 2. Create Order Items
                foreach ($request->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity']
                    ]);
                }

                // 3. Create Payment (Simulated)
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $request->total_amount,
                    'status' => 'pending',
                    'transaction_reference' => 'TXN-' . strtoupper(Str::random(12))
                ]);

                return response()->json($order->load('items', 'payment'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo procesar el pedido: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $user = Auth::guard('api')->user();

        $order = Order::with(['items.product', 'payment'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($order);
    }
}
