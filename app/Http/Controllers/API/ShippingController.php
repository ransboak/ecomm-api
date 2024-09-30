<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    //
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'shipping_method' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        // Ensure the order exists
        $order = Order::findOrFail($orderId);

        // Create shipping details
        $shipping = Shipping::create([
            'order_id' => $order->id,
            'shipping_address' => $request->input('shipping_address'),
            'shipping_method' => $request->input('shipping_method'),
            'shipping_cost' => $request->input('shipping_cost'),
        ]);

        return response()->json($shipping, 201);
    }

    // Retrieve shipping details for a specific order
    public function show($orderId)
    {
        // Ensure the order exists
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Retrieve the shipping details for this order
        $shipping = Shipping::where('order_id', $order->id)->firstOrFail();

        return response()->json($shipping);
    }

    // Update shipping details
    public function update(Request $request, $id)
    {
        $shipping = Shipping::findOrFail($id);

        $request->validate([
            'shipping_address' => 'sometimes|required|string',
            'shipping_method' => 'sometimes|required|string',
            'shipping_cost' => 'sometimes|required|numeric|min:0',
        ]);

        // Update the shipping details
        $shipping->update($request->only('shipping_address', 'shipping_method', 'shipping_cost'));

        return response()->json($shipping);
    }

    // Delete shipping details
    public function destroy($id)
    {
        $shipping = Shipping::findOrFail($id);

        $shipping->delete();

        return response()->json(['message' => 'Shipping details deleted successfully.']);
    }
}
