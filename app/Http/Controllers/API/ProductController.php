<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            // 'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate images
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create product
        $product = Product::create($request->only('name', 'description', 'price', 'stock', 'category_id', 'brand_id'));

        // Handle image uploads
        if($request->has('images')){
            foreach ($request->images as $image) {
                $url = $image->store('products'); // Store the image
                $product->images()->create(['url' => $url]); // Create product image record
            }
        }

        return response()->json($product, 201);
    }

    // Get all products
    public function index()
    {
        $products = Product::with(['images', 'category', 'brand'])->get();
        return response()->json($products);
    }

    // Get a specific product
    public function show($id)
    {
        $product = Product::with(['images', 'category', 'brand'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
        return response()->json($product);
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update($request->only('name', 'description', 'price', 'stock', 'category_id', 'brand_id'));

        if ($request->has('images')) {
            $product->images()->delete();

            foreach ($request->images as $image) {
                $url = $image->store('products');
                $product->images()->create(['url' => $url]);
            }
        }

        $product->name = $request->name;
        $product->slug = Product::generateUniqueSlug($product->name);
        $product->save();

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->images()->delete();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
