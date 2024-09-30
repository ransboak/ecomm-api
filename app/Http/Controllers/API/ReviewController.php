<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    //
  // Add a new review to a product
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Ensure product exists
        $product = Product::findOrFail($productId);

        // Create a new review
        $review = Review::create([
            'product_id' => $productId,
            'customer_id' => 1, // assuming the user is authenticated
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return response()->json($review, 201);
    }

    // Get all reviews for a specific product
    public function index($productId)
    {
        // Ensure product exists
        $product = Product::findOrFail($productId);

        // Retrieve all reviews for this product
        $reviews = $product->reviews()->with('customer')->get();

        return response()->json($reviews);
    }

    // Update a review
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Ensure that the logged-in user is the one who made the review
        if (Auth::id() !== $review->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string',
        ]);

        // Update the review
        $review->update($request->only('rating', 'comment'));

        return response()->json($review);
    }

    // Delete a review
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Ensure that the logged-in user is the one who made the review
        if (Auth::id() !== $review->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    
}
}