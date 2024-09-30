<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'logo_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate as image
        ]);

        // If validation fails, return a clear error response with status code 422
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the file upload if a logo is provided
        $logoPath = null;
        if ($request->hasFile('logo_url')) {
            $logoPath = $request->file('logo_url')->store('logos', 'public'); // Store the logo in the 'logos' directory
        }

        // Create a new brand
        $brand = Brand::create([
            'name' => $request->name,
            'logo_url' => $logoPath, // Save the path to the logo
        ]);

        // Return a successful response
        return response()->json($brand, 201);
    }

    public function index()
    {
        $brands = Brand::all();
        return response()->json($brands, 200);
    }

    // Get a specific brand by ID
    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found.'], 404);
        }
        // Make sure to return the full URL for the logo if it exists
        if ($brand->logo_url) {
            $brand->logo_url = Storage::url($brand->logo_url);
        }

        return response()->json($brand, 200);
    }

    // Update a brand by ID
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $id,
            'logo_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate as image
        ]);

        // If validation fails, return a clear error response with status code 422
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the brand
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found.'], 404);
        }

        // Handle the file upload if a new logo is provided
        if ($request->hasFile('logo_url')) {
            // Delete the old logo if it exists
            if ($brand->logo_url) {
                Storage::disk('public')->delete($brand->logo_url);
            }
            // Store the new logo
            $logoPath = $request->file('logo_url')->store('logos', 'public');
            $brand->logo_url = $logoPath; // Update the logo path
        }

        // Update the brand
        $brand->name = $request->name;
        $brand->slug = Brand::generateUniqueSlug($brand->name);
        $brand->save();

        // Return a successful response
        return response()->json($brand, 200);
    }

    // Delete a brand by ID
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found.'], 404);
        }

        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully.'], 200);
    }
}
