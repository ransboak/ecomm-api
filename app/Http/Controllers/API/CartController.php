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
    // Add a product to the cart
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

        // Get or create the cart for the customer
        $cart = Cart::firstOrCreate(['customer_id' => $customerId]);

        // Check if the product is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();

        if ($cartItem) {
            // If product already exists in the cart, update the quantity
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Otherwise, add the product to the cart
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully.']);
    }

    // View all items in the cart
    public function viewCart($customerId)
    {
        // Get the customer's cart
        $cart = Cart::where('customer_id', $customerId)->with('items.product')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty.'], 404);
        }

        // Calculate the total price of the cart
        $totalPrice = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'cart_items' => $cart->items,
            'total_price' => $totalPrice,
        ]);
    }

    // Increment quantity of a cart item
    public function incrementQuantity($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if(!$cartItem){
            return response()->json(['message' => 'Cart Item not found']);
        }

        // Increment the quantity
        $cartItem->quantity += 1;
        $cartItem->save();

        return response()->json(['message' => 'Item quantity incremented successfully.', 'cart_item' => $cartItem]);
    }

    // Decrement quantity of a cart item
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
            // If quantity is 1, remove the item from the cart
            $cartItem->delete();

            return response()->json(['message' => 'Item removed from cart.']);
        }
    }

    // Remove an item from the cart
    public function removeItem($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart successfully.']);
    }

    // Clear the entire cart
    public function clearCart($customerId)
    {
        $cart = Cart::where('customer_id', $customerId)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared successfully.']);
    }
}
