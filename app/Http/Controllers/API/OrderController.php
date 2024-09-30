<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    //
    public function create(Request $request)
{
    $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    $totalPrice = 0; // Initialize total price

    // Calculate total price based on items
    foreach ($request->items as $item) {
        $product = Product::findOrFail($item['product_id']); // Get the product
        $totalPrice += $product->price * $item['quantity']; // Calculate total price
    }

    // Create the order with the calculated total price
    $order = Order::create([
        'user_id' => Auth::id(),
        'customer_id' => Auth::user()->customer->id, // Ensure the user has a customer record
        'total_price' => $totalPrice,
    ]);

    // Create order items
    foreach ($request->items as $item) {
        $product = Product::findOrFail($item['product_id']);
        $order->orderItems()->create([
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            // Additional fields like price can be added here
        ]);
    }

    return response()->json($order, 201);
}

// Get all orders
public function index()
{
    $orders = Order::with('orderItems')->get();
    return response()->json($orders);
}

// Get a single order
public function show($id)
{
    $order = Order::with('orderItems')->findOrFail($id);
    return response()->json($order);
}

// Update order status
public function update(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,processing,completed,cancelled',
    ]);

    $order = Order::findOrFail($id);
    $order->status = $request->status;
    $order->save();

    return response()->json($order);
}

// Delete an order
public function destroy($id)
{
    $order = Order::findOrFail($id);
    $order->delete();
    return response()->json(['message' => 'Order deleted successfully.']);
}
}
