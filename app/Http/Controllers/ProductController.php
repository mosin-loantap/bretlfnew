<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gte:min_amount',
            'min_tenure' => 'required|integer|min:1',
            'max_tenure' => 'required|integer|gte:min_tenure',
            'interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gte:min_amount',
            'min_tenure' => 'required|integer|min:1',
            'max_tenure' => 'required|integer|gte:min_tenure',
            'interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        $product->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
