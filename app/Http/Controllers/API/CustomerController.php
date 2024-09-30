<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    //
    public function profile()
    {
        return response()->json(auth()->user()->customer, 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        $customer->update($request->all());
        return response()->json($customer, 200);
    }
}
