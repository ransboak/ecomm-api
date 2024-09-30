<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function index()
    {
        $cart = auth()->user()->cart()->with('items.product')->firstOrFail();

        return response()->json($cart);
    }

    // Add a product to the cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id()
        ]);

        // Check if the product is already in the cart
        $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            // Update quantity if the item already exists
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            // Otherwise, create a new cart item
            $product = Product::find($request->product_id);

            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    // Remove a product from the cart
    public function remove($id)
    {
        $cart = auth()->user()->cart()->firstOrFail();
        $cartItem = $cart->items()->where('id', $id)->firstOrFail();

        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart']);
    }

    // Update the quantity of an item in the cart
    public function updateItemQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('id', $id)->firstOrFail();
        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json(['message' => 'Cart item quantity updated']);
    }
}
