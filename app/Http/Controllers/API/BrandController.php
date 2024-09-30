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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'logo_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $logoPath = null;
        if ($request->hasFile('logo_url')) {
            $logoPath = $request->file('logo_url')->store('logos', 'public');
        }

        $brand = Brand::create([
            'name' => $request->name,
            'logo_url' => $logoPath,
        ]);

        return response()->json($brand, 201);
    }

    public function index()
    {
        $brands = Brand::all();
        return response()->json($brands, 200);
    }

    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found.'], 404);
        }
 
        if ($brand->logo_url) {
            $brand->logo_url = Storage::url($brand->logo_url);
        }

        return response()->json($brand, 200);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $id,
            'logo_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found.'], 404);
        }

        if ($request->hasFile('logo_url')) {
            if ($brand->logo_url) {
                Storage::disk('public')->delete($brand->logo_url);
            }

            $logoPath = $request->file('logo_url')->store('logos', 'public');
            $brand->logo_url = $logoPath;
        }

        $brand->name = $request->name;
        $brand->slug = Brand::generateUniqueSlug($brand->name);
        $brand->save();

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
