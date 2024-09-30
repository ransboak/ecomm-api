<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function addToCart(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $customerId = $request->input('customer_id');
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = Cart::firstOrCreate(['customer_id' => $customerId]);

        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully.']);
    }

    public function viewCart($customerId)
    {
        $cart = Cart::where('customer_id', $customerId)->with('items.product')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty.'], 404);
        }

        $totalPrice = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'cart_items' => $cart->items,
            'total_price' => $totalPrice,
        ]);
    }

    public function incrementQuantity($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if(!$cartItem){
            return response()->json(['message' => 'Cart Item not found']);
        }

        $cartItem->quantity += 1;
        $cartItem->save();

        return response()->json(['message' => 'Item quantity incremented successfully.', 'cart_item' => $cartItem]);
    }

    public function decrementQuantity($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if(!$cartItem){
            return response()->json(['message' => 'Cart Item not found']);
        }

        if ($cartItem->quantity > 1) {
            // Decrement the quantity
            $cartItem->quantity -= 1;
            $cartItem->save();

            return response()->json(['message' => 'Item quantity decremented successfully.', 'cart_item' => $cartItem]);
        } else {
            $cartItem->delete();

            return response()->json(['message' => 'Item removed from cart.']);
        }
    }

    public function removeItem($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart successfully.']);
    }

    public function clearCart($customerId)
    {
        $cart = Cart::where('customer_id', $customerId)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared successfully.']);
    }
}
