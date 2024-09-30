<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    //
    // public function apply(Request $request)
    // {
    //     $coupon = Discount::where('code', $request->code)->firstOrFail();

    //     if ($coupon->isValid()) {
    //         return response()->json([
    //             'discount' => $coupon->discount,
    //             'message' => 'Coupon applied successfully.'
    //         ]);
    //     }

    //     return response()->json(['message' => 'Invalid or expired coupon.'], 400);
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:discounts',
            'discount' => 'required|numeric|min:0|max:100', // Assuming percentage
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $discount = Discount::create($request->all());

        return response()->json($discount, 201);
    }

    // Update a discount by ID
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:discounts,code,' . $id,
            'discount' => 'required|numeric|min:0|max:100',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found.'], 404);
        }

        $discount->update($request->all());
        return response()->json($discount, 200);
    }

    // Delete a discount by ID
    public function destroy($id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found.'], 404);
        }

        $discount->delete();
        return response()->json(['message' => 'Discount deleted successfully.'], 200);
    }

    // List all discounts
    public function index()
    {
        $discounts = Discount::all();
        return response()->json($discounts, 200);
    }

    // Retrieve a specific discount by ID
    public function show($id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found.'], 404);
        }

        return response()->json($discount, 200);
    }

    // Apply a discount code
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:discounts,code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $discount = Discount::where('code', $request->code)->first();

        if (!$discount->isValid()) {
            return response()->json(['message' => 'This discount is either expired or has reached its usage limit.'], 400);
        }

        $discount->increment('uses');

        return response()->json(['discount' => $discount->discount], 200);
    }
}
