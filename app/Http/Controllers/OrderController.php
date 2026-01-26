<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
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
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
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

                // 2. Create Order Items and Discount Stock
                foreach ($request->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'product_name' => $item['product_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity']
                    ]);

                    // Discount Stock
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock', $item['quantity']);
                    }

                    if (isset($item['variant_id']) && $item['variant_id']) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $variant->decrement('stock', $item['quantity']);
                        }
                    }
                }

                // 3. Create Payment
                $paymentData = [
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $request->total_amount,
                    'status' => 'pending',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12))
                ];

                if ($request->payment_method === 'yape' && $request->hasFile('proof_image')) {
                    $path = $request->file('proof_image')->store('payments', 'public');
                    $paymentData['proof_image'] = asset('storage/' . $path);
                    $order->status = 'pending_validation';
                    $order->save();
                }

                Payment::create($paymentData);

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

        // If admin, allow seeing any order, else only own
        // Note: Ideally middleware handles roles, but here we can check if user is admin or owner
        // For now, assuming standard user route. We will make a separate adminShow if needed or modify this.

        $order = Order::with(['items.product', 'payment'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    // Admin Methods

    public function adminIndex()
    {
        $orders = Order::with(['user', 'items', 'payment'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function adminShow($id)
    {
        $order = Order::with(['user', 'items.product', 'payment'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,pending_validation,processing,shipped,delivered,cancelled,paid,rejected_payment'
        ]);

        $order = Order::with('items')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If status changes TO cancelled (or rejected_payment) FROM something else that was NOT cancelled
        if (
            ($newStatus === 'cancelled' || $newStatus === 'rejected_payment') &&
            ($oldStatus !== 'cancelled' && $oldStatus !== 'rejected_payment')
        ) {

            // Return stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                }
            }
        }
        // If it was cancelled/rejected and now is being restored to a valid state
        elseif (
            ($oldStatus === 'cancelled' || $oldStatus === 'rejected_payment') &&
            ($newStatus !== 'cancelled' && $newStatus !== 'rejected_payment')
        ) {

            // Discount stock again
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                }
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->decrement('stock', $item->quantity);
                    }
                }
            }
        }

        $order->status = $newStatus;
        $order->save();

        return response()->json($order);
    }
}
